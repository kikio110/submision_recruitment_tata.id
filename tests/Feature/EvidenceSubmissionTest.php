<?php
namespace Tests\Feature;

use App\Models\{EvidenceRecord, EvidenceSubmission, Setting, User};
use App\Support\CurrentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class EvidenceSubmissionTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    private function pm(): User { return User::where('role', 'PROJECT_MANAGER')->first(); }
    private function asPm(): void { CurrentUser::set(null); $this->withSession(['demo_user_id' => $this->pm()->id]); }
    private function asDirector(): void { CurrentUser::set(null); $this->withSession(['demo_user_id' => User::where('role', 'DIRECTOR')->first()->id]); }

    private function payload(array $o = []): array
    {
        return $o + ['metric_key' => 'scanner_status', 'subject_key' => 'SCN-B', 'observed_value' => 'running', 'observed_at' => '2026-10-07T09:00', 'source_system' => 'scanner_production_log',
            'source_note' => 'Tangkapan layar monitor scanner jam 09:00'];
    }

    /** PRJ-002's PIC is not the demo PM, so the PM is made PIC of that project for these scenarios. */
    private function prepareScannerScenario(): array
    {
        $this->seedDataset();
        $p = $this->project('PRJ-002');
        $p->update(['pic' => $this->pm()->pic_name]);
        $finding = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $fu = $this->followUpFor($finding, $this->pm()->pic_name);
        Setting::write('as_of', '2026-10-07 12:00:00');
        app(\App\Services\DataImportService::class)->reprocessAll();
        return [$p, $finding, $fu];
    }

    public function test_pm_submission_stays_pending_and_does_not_count_as_evidence_or_verify_anything(): void
    {
        [$p, $finding, $fu] = $this->prepareScannerScenario();
        $before = EvidenceRecord::count();
        $this->asPm();
        $this->post("/projects/{$p->id}/evidence", $this->payload(['follow_up_id' => $fu->id]))->assertRedirect();
        $s = EvidenceSubmission::firstOrFail();
        $this->assertSame('PENDING', $s->status);
        $this->assertSame('RUNNING', $s->observed_value);
        $this->assertSame($before, EvidenceRecord::count(), 'pending submissions never become evidence');
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED']);
        $this->assertSame('UNVERIFIED', $finding->fresh()->verification_status, 'a pending submission cannot verify the issue');
    }

    public function test_director_approval_turns_it_into_operational_evidence_and_verifies_after_rerun(): void
    {
        [$p, $finding, $fu] = $this->prepareScannerScenario();
        $this->asPm();
        $this->post("/projects/{$p->id}/evidence", $this->payload(['follow_up_id' => $fu->id]));
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED']);
        $this->assertSame('UNVERIFIED', $finding->fresh()->verification_status);
        $s = EvidenceSubmission::firstOrFail();

        $this->asDirector();
        $this->post("/evidence-submissions/{$s->id}/approve", ['review_note' => 'Sesuai log monitor'])->assertRedirect();
        $s->refresh();
        $this->assertSame('APPROVED', $s->status);
        $e = EvidenceRecord::where('source_ref', 'SUB-' . $s->id)->where('metric_key', 'scanner_status')->firstOrFail();
        $this->assertSame('OPERATIONAL', $e->evidence_class);
        $this->assertSame('SCN-B', $e->subject_key);
        $this->assertStringContainsString('diajukan', $e->source_label);
        $this->assertStringContainsString('disetujui', $e->source_label);
        $this->assertSame('VERIFIED', $finding->fresh()->verification_status, 'approval re-runs verification for the RESOLVED follow-up');
        $events = $fu->histories()->pluck('event')->all();
        $this->assertContains('EVIDENCE_SUBMITTED', $events);
        $this->assertContains('EVIDENCE_REVIEWED', $events);
    }

    public function test_approved_evidence_that_shows_the_issue_persists_reopens_it(): void
    {
        [$p, $finding, $fu] = $this->prepareScannerScenario();
        $this->asPm();
        $this->post("/projects/{$p->id}/evidence", $this->payload(['follow_up_id' => $fu->id, 'observed_value' => 'OUT_OF_SERVICE']));
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED']);
        $this->asDirector();
        $this->post('/evidence-submissions/' . EvidenceSubmission::firstOrFail()->id . '/approve');
        $this->assertSame('REOPENED', $finding->fresh()->verification_status);
    }

    public function test_rejection_requires_a_reason_and_creates_no_evidence(): void
    {
        [$p, , $fu] = $this->prepareScannerScenario();
        $this->asPm();
        $this->post("/projects/{$p->id}/evidence", $this->payload(['follow_up_id' => $fu->id]));
        $s = EvidenceSubmission::firstOrFail();
        $before = EvidenceRecord::count();
        $this->asDirector();
        $this->post("/evidence-submissions/{$s->id}/reject", [])->assertSessionHasErrors('review_note');
        $this->post("/evidence-submissions/{$s->id}/reject", ['review_note' => 'Tangkapan layar tidak menampilkan waktu'])->assertRedirect();
        $this->assertSame('REJECTED', $s->fresh()->status);
        $this->assertSame($before, EvidenceRecord::count());
        $this->post("/evidence-submissions/{$s->id}/approve")->assertSessionHasErrors('review_note'); // already reviewed
    }

    public function test_access_control_and_validation(): void
    {
        [$p] = $this->prepareScannerScenario();
        $other = \App\Models\Project::where('pic', '!=', $this->pm()->pic_name)->first();
        $this->asPm();
        $this->post("/projects/{$other->id}/evidence", $this->payload())->assertForbidden();
        $this->get("/projects/{$other->id}/evidence/create")->assertForbidden();
        $this->post("/projects/{$p->id}/evidence", $this->payload())->assertRedirect();
        $s = EvidenceSubmission::firstOrFail();
        $this->post("/evidence-submissions/{$s->id}/approve")->assertForbidden();
        $this->post("/evidence-submissions/{$s->id}/reject", ['review_note' => 'x y z'])->assertForbidden();
        // validation
        $this->post("/projects/{$p->id}/evidence", $this->payload(['observed_at' => '2026-10-09T09:00']))->assertSessionHasErrors('observed_at'); // beyond as-of
        $this->post("/projects/{$p->id}/evidence", $this->payload(['subject_key' => '']))->assertSessionHasErrors('subject_key');
        $this->post("/projects/{$p->id}/evidence", $this->payload(['source_note' => '']))->assertSessionHasErrors('source_note');
        $this->post("/projects/{$p->id}/evidence", ['metric_key' => 'verified_quantity', 'observed_value' => 'banyak', 'observed_at' => '2026-10-06T10:00', 'source_system' => 'x', 'source_note' => 'catatan uji'])->assertSessionHasErrors('observed_value');
        $this->post("/projects/{$p->id}/evidence", $this->payload(['metric_key' => 'tidak_ada']))->assertSessionHasErrors('metric_key');
    }

    public function test_director_submission_is_approved_immediately_and_queue_pages_render(): void
    {
        [$p] = $this->prepareScannerScenario();
        $this->post("/projects/{$p->id}/evidence", ['metric_key' => 'verified_quantity', 'observed_value' => '120000', 'unit' => 'pages', 'observed_at' => '2026-10-07T08:00',
            'source_system' => 'scanner_production_log', 'source_note' => 'Rekap log harian'])->assertRedirect();
        $s = EvidenceSubmission::firstOrFail();
        $this->assertSame('APPROVED', $s->status);
        $this->assertSame(120000.0, EvidenceRecord::where('source_ref', 'SUB-' . $s->id)->where('metric_key', 'verified_quantity')->first()->numeric_value);
        foreach (['/evidence-submissions', '/evidence-submissions?status=APPROVED', '/evidence-submissions?status=ALL', "/projects/{$p->id}", "/projects/{$p->id}/evidence/create"] as $u) $this->get($u)->assertOk();
        $this->get('/evidence-submissions?status=APPROVED')->assertSee('Disetujui')->assertSee('Rekap log harian');
    }

    public function test_dashboard_alerts_director_about_pending_submissions(): void
    {
        [$p] = $this->prepareScannerScenario();
        $this->asPm();
        $this->post("/projects/{$p->id}/evidence", $this->payload());
        $this->asDirector();
        $this->get('/dashboard')->assertSee('menunggu tinjauan Anda');
    }
}
