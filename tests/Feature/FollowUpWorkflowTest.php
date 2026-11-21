<?php
namespace Tests\Feature;

use App\Models\{ActivityLog, FollowUp, User};
use App\Support\CurrentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class FollowUpWorkflowTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    public function test_director_creates_follow_up_and_it_is_persisted_with_history_and_actor(): void
    {
        $this->seedDataset();
        $f = $this->finding('PRJ-005', 'missing_prerequisite');
        $this->post('/follow-ups', ['project_id' => $f->project_id, 'finding_id' => $f->id, 'title' => 'Minta kredensial production', 'description' => 'Eskalasi ke customer',
            'assigned_pic' => 'Raka', 'priority' => 'HIGH', 'due_date' => '2026-10-09'])->assertRedirect();
        $fu = FollowUp::firstOrFail();
        $this->assertSame('OPEN', $fu->status);
        $this->assertSame('Raka', $fu->assigned_pic);
        $this->assertNotNull($fu->created_by);
        $this->assertSame('CREATED', $fu->histories()->first()->event);
        $this->assertTrue(ActivityLog::where('type', 'FOLLOW_UP_CREATED')->exists());
    }

    public function test_create_form_validates_and_project_manager_cannot_create(): void
    {
        $this->seedDataset();
        $this->post('/follow-ups', ['title' => ''])->assertSessionHasErrors(['project_id', 'title', 'assigned_pic', 'priority']);
        $this->withSession(['demo_user_id' => User::where('role', 'PROJECT_MANAGER')->first()->id]);
        CurrentUser::set(null);
        $this->post('/follow-ups', ['project_id' => 1, 'title' => 'x', 'assigned_pic' => 'y', 'priority' => 'LOW'])->assertForbidden();
    }

    public function test_status_notes_and_invalid_transition(): void
    {
        $this->seedDataset();
        $fu = $this->followUpFor($this->finding('PRJ-004', 'data_quality', 'quality:records_skipped_missing_region'), 'Raka');
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'IN_PROGRESS'])->assertRedirect();
        $this->post("/follow-ups/{$fu->id}/notes", ['note' => 'Sudah menghubungi tim data wilayah'])->assertRedirect();
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'IN_PROGRESS'])->assertSessionHasErrors('status'); // same -> invalid
        $events = $fu->histories()->pluck('event')->all();
        $this->assertContains('STATUS_CHANGED', $events);
        $this->assertContains('NOTE_ADDED', $events);
        $this->assertSame('IN_PROGRESS', $fu->fresh()->status);
    }

    public function test_project_manager_only_updates_own_follow_ups(): void
    {
        $this->seedDataset();
        $pm = User::where('role', 'PROJECT_MANAGER')->first();
        $mine = $this->followUpFor($this->finding('PRJ-005', 'missing_prerequisite'), $pm->pic_name);
        $other = $this->followUpFor($this->finding('PRJ-004', 'data_quality', 'master_internal'), 'Nadia');
        CurrentUser::set(null);
        $this->withSession(['demo_user_id' => $pm->id]);
        $this->post("/follow-ups/{$mine->id}/status", ['status' => 'IN_PROGRESS'])->assertRedirect();
        $this->post("/follow-ups/{$other->id}/status", ['status' => 'IN_PROGRESS'])->assertForbidden();
        $this->get('/follow-ups')->assertSee($mine->title)->assertDontSee($other->title);
    }

    public function test_resolved_with_insufficient_evidence_results_in_unverified_and_never_verified(): void
    {
        $this->seedDataset();
        $finding = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $fu = $this->followUpFor($finding);
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED', 'note' => 'Scanner B sudah diperbaiki'])->assertRedirect();
        $this->assertSame('RESOLVED', $fu->fresh()->status);               // action finished…
        $this->assertSame('UNVERIFIED', $finding->fresh()->verification_status); // …but issue is NOT verified
        $v = $finding->verifications()->first();
        $this->assertSame('UNVERIFIED', $v->result);
        $this->assertStringContainsString('Belum ada evidence operasional', $v->explanation);
        $this->get("/follow-ups/{$fu->id}")->assertOk()->assertSee('Verify again')->assertSee('Unverified');
    }

    public function test_resolved_with_sufficient_new_operational_evidence_can_be_verified(): void
    {
        $this->seedDataset();
        $finding = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $fu = $this->followUpFor($finding);
        $this->import('prod2.json', $this->prodJson([[ 'source_record_id' => 'SCAN-BETA-004', 'project_id' => 'PRJ-002', 'source_system' => 'scanner_production_log',
            'record_type' => 'equipment_status', 'timestamp' => '2026-10-07T09:00:00', 'scanners' => [['scanner_id' => 'SCN-B', 'status' => 'RUNNING']]]]), '2026-10-07 12:00:00');
        $this->assertFalse($finding->fresh()->is_active, 'rule no longer detects the issue once SCN-B is RUNNING');
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED'])->assertRedirect();
        $this->assertSame('VERIFIED', $finding->fresh()->verification_status);
        $this->assertStringContainsString('SCAN-BETA-004', $finding->verifications()->first()->explanation);
    }

    public function test_new_evidence_showing_issue_persists_reopens_instead_of_verifying(): void
    {
        $this->seedDataset();
        $finding = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $fu = $this->followUpFor($finding);
        $this->import('prod2.json', $this->prodJson([[ 'source_record_id' => 'SCAN-BETA-005', 'project_id' => 'PRJ-002', 'source_system' => 'scanner_production_log',
            'record_type' => 'equipment_status', 'timestamp' => '2026-10-07T09:00:00', 'scanners' => [['scanner_id' => 'SCN-B', 'status' => 'OUT_OF_SERVICE']]]]), '2026-10-07 12:00:00');
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED']);
        $this->assertSame('REOPENED', $finding->fresh()->verification_status);
    }

    public function test_stale_new_evidence_is_not_sufficient(): void
    {
        $this->seedDataset();
        $finding = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $fu = $this->followUpFor($finding);
        $this->import('prod2.json', $this->prodJson([[ 'source_record_id' => 'SCAN-BETA-006', 'project_id' => 'PRJ-002', 'source_system' => 'scanner_production_log',
            'record_type' => 'equipment_status', 'timestamp' => '2026-10-06T16:00:00', 'scanners' => [['scanner_id' => 'SCN-B', 'status' => 'RUNNING']]]]), '2026-10-12 12:00:00');
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED']);
        $this->assertSame('UNVERIFIED', $finding->fresh()->verification_status);
        $this->assertStringContainsString('usang', $finding->verifications()->first()->explanation);
    }
}
