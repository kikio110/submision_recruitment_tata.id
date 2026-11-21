<?php
namespace Tests\Feature;

use App\Models\{DataImportBatch, EvidenceRecord, Finding, Project, ProjectUpdate, User};
use App\Support\CurrentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class ChangeReadinessTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    private function pm(): User { return User::where('role', 'PROJECT_MANAGER')->first(); }

    private function asPm(): void { CurrentUser::set(null); $this->withSession(['demo_user_id' => $this->pm()->id]); }

    private function projectForm(array $o = []): array
    {
        return $o + ['project_id' => 'PRJ-050', 'project_name' => 'Proyek Baru', 'customer' => 'Instansi Baru', 'project_type' => 'Uji', 'pic' => 'Raka', 'start_date' => '2026-09-20',
            'deadline' => '2026-10-20', 'target' => 100, 'target_unit' => 'records', 'reported_actual' => 20, 'reported_progress_pct' => 20, 'reported_status' => 'ON TRACK'];
    }

    public function test_director_adds_a_project_manually_and_it_is_analysed_by_the_same_rules_with_impact_recorded(): void
    {
        $this->seedDataset();
        $this->post('/projects', $this->projectForm())->assertRedirect();
        $p = $this->project('PRJ-050');
        $this->assertNotNull($p->currentAssessment);
        $this->assertTrue(Finding::where('project_id', $p->id)->where('category', 'INSUFFICIENT_EVIDENCE')->exists(), 'no operational evidence => uncertainty finding, not safe');
        $batch = DataImportBatch::where('origin', 'MANUAL')->latest('id')->first();
        $this->assertSame('PROJECT_MASTER', $batch->source_kind);
        $row = collect($batch->impact['projects'])->firstWhere('code', 'PRJ-050');
        $this->assertTrue($row['new_project']);
        $this->get("/data-management/imports/{$batch->id}")->assertOk()->assertSee('Dampak terhadap analisis')->assertSee('Proyek baru');
    }

    public function test_project_form_validates_and_only_director_can_manage_projects(): void
    {
        $this->seedDataset();
        $this->post('/projects', $this->projectForm(['project_id' => 'PRJ-001']))->assertSessionHasErrors('project_id');
        $this->post('/projects', $this->projectForm(['deadline' => '2026-09-01', 'target' => 0]))->assertSessionHasErrors(['deadline', 'target']);
        $this->post('/projects', $this->projectForm(['reported_progress_pct' => 140]))->assertSessionHasErrors('reported_progress_pct');
        $this->asPm();
        $this->post('/projects', $this->projectForm())->assertForbidden();
        $this->get('/projects/create')->assertForbidden();
    }

    public function test_editing_a_project_is_a_revision_and_impact_shows_the_change(): void
    {
        $this->seedDataset();
        $p = $this->project('PRJ-004');
        $before = $p->currentAssessment->score;
        $form = fn (array $o) => array_diff_key($this->projectForm($o), ['project_id' => 1]);
        $this->put("/projects/{$p->id}", $form(['project_name' => $p->name, 'customer' => $p->customer, 'project_type' => $p->project_type, 'pic' => $p->pic,
            'start_date' => $p->start_date->toDateString(), 'deadline' => '2026-10-08', 'target' => $p->target, 'target_unit' => $p->target_unit,
            'reported_actual' => $p->reported_actual, 'reported_progress_pct' => $p->reported_progress_pct, 'reported_status' => $p->reported_status]))->assertRedirect();
        $p->refresh();
        $this->assertSame(2, $p->revision);
        $this->assertSame('2026-10-08', $p->deadline->toDateString());
        $this->assertGreaterThan($before, $p->currentAssessment->fresh()->score, 'a nearer deadline raises deadline pressure');
        $this->assertSame(2, EvidenceRecord::where('project_id', $p->id)->where('metric_key', 'reported_progress_pct')->count(), 'old master evidence is kept next to the new revision');
        $b = DataImportBatch::where('origin', 'MANUAL')->latest('id')->first();
        $this->assertSame(1, $b->conflicting_records); // flagged as revision, previous values kept
    }

    public function test_project_with_follow_ups_cannot_be_deleted_but_a_clean_one_can(): void
    {
        $this->seedDataset();
        $f = $this->followUpFor($this->finding('PRJ-005', 'missing_prerequisite'));
        $this->delete("/projects/{$f->project_id}")->assertSessionHas('error');
        $this->assertTrue(Project::whereKey($f->project_id)->exists());
        $this->post('/projects', $this->projectForm());
        $this->delete('/projects/' . $this->project('PRJ-050')->id)->assertRedirect('/projects');
        $this->assertFalse(Project::where('project_code', 'PRJ-050')->exists());
    }

    public function test_pic_report_appears_on_timeline_as_reported_and_never_verifies_an_issue(): void
    {
        $this->seedDataset();
        $p = $this->project('PRJ-001'); // PIC = Raka = demo PM
        $finding = $this->finding('PRJ-001', 'pending_quantity');
        $fu = $this->followUpFor($finding, 'Raka');
        $this->asPm();
        $this->get("/projects/{$p->id}/report")->assertOk();
        $this->post("/projects/{$p->id}/report", ['message' => 'Seluruh box pending sudah dicek ulang dan disetujui customer.', 'reported_at' => '2026-10-06T22:00', 'source_type' => 'PROJECT_MANAGER',
            'reported_progress_pct' => 82])->assertRedirect();
        $u = ProjectUpdate::where('project_id', $p->id)->where('update_code', 'like', 'UPD-M-%')->firstOrFail();
        $e = EvidenceRecord::where('origin_table', 'project_updates')->where('origin_id', $u->id)->first();
        $this->assertSame('REPORTED', $e->evidence_class);
        $this->assertSame(82.0, $p->fresh()->reported_progress_pct);
        $this->get("/projects/{$p->id}")->assertSee('Seluruh box pending sudah dicek ulang')->assertSee('Dilaporkan');
        $this->post("/follow-ups/{$fu->id}/status", ['status' => 'RESOLVED']);
        $this->assertNotSame('VERIFIED', $finding->fresh()->verification_status, 'a report is a claim, not operational evidence');
    }

    public function test_pm_cannot_report_on_a_project_owned_by_another_pic(): void
    {
        $this->seedDataset();
        $other = Project::where('pic', '!=', 'Raka')->first();
        $this->asPm();
        $this->get("/projects/{$other->id}/report")->assertForbidden();
        $this->post("/projects/{$other->id}/report", ['message' => 'Laporan tidak berwenang', 'reported_at' => '2026-10-06T22:00', 'source_type' => 'PROJECT_MANAGER'])->assertForbidden();
        $this->post("/projects/" . $this->project('PRJ-001')->id . '/report', ['message' => 'pendek', 'reported_at' => '2026-10-06T22:00', 'source_type' => 'PROJECT_MANAGER'])->assertSessionHasErrors('message');
    }

    public function test_follow_up_note_can_be_reported_to_the_project_timeline(): void
    {
        $this->seedDataset();
        $fu = $this->followUpFor($this->finding('PRJ-005', 'missing_prerequisite'), 'Raka');
        $this->post("/follow-ups/{$fu->id}/notes", ['note' => 'Customer berjanji mengirim kredensial besok pagi.', 'report_to_timeline' => 1])->assertRedirect();
        $this->assertTrue(ProjectUpdate::where('project_id', $fu->project_id)->where('message', 'like', "%[Follow-up #{$fu->id}%kredensial%")->exists());
        $this->post("/follow-ups/{$fu->id}/notes", ['note' => 'Catatan internal saja tanpa laporan.'])->assertRedirect();
        $this->assertFalse(ProjectUpdate::where('message', 'like', '%internal saja%')->exists());
    }

    public function test_unseen_status_value_and_new_metric_are_absorbed_without_new_rules(): void
    {
        $this->seedDataset();
        $this->import('new.json', $this->prodJson([[ 'source_record_id' => 'SCAN-BETA-020', 'project_id' => 'PRJ-002', 'source_system' => 'scanner_production_log', 'record_type' => 'equipment_status',
            'timestamp' => '2026-10-07T09:00:00', 'scanners' => [['scanner_id' => 'SCN-B', 'status' => 'MAINTENANCE']], 'humidity_percent' => 61]]), '2026-10-07 12:00:00');
        $f = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $this->assertTrue($f->is_active);
        $this->assertSame('MEDIUM', $f->severity, 'unknown status is treated as unhealthy, not ignored');
        $this->assertStringContainsString('MAINTENANCE', $f->title);
        $e = EvidenceRecord::where('metric_key', 'humidity_percent')->first();
        $this->assertNotNull($e, 'unknown field is kept as evidence');
        $this->assertSame('OTHER', $e->category);
    }

    public function test_changing_the_as_of_date_recomputes_analysis(): void
    {
        $this->seedDataset();
        $p = $this->project('PRJ-002');
        $before = $p->currentAssessment->score;
        $this->post('/settings', ['as_of' => '2026-10-09 12:00:00'])->assertRedirect();
        $this->assertNotEquals($before, $p->fresh()->currentAssessment->score);
    }

    public function test_settings_page_shows_decisions_without_technical_jargon(): void
    {
        $this->seedDataset();
        $r = $this->get('/settings')->assertOk()->assertSee('Keputusan desain')->assertSee('Tinjau ulang bila')->assertSee('Batas evidence usang');
        $r->assertDontSee('stale_hours')->assertDontSee('evidence_records');
        $this->get('/dashboard')->assertDontSee('dari database');
    }

    public function test_lexicon_change_applies_to_stored_updates_after_recompute(): void
    {
        $this->seedDataset();
        $e = EvidenceRecord::where('source_ref', 'UPD-022')->first();
        $this->assertNotContains('HOLD', $e->signalList());
        $needle = mb_strtolower(explode(' ', $e->summary)[0]);
        config(['ontrack.lexicon.HOLD' => array_merge(config('ontrack.lexicon.HOLD'), [$needle])]);
        app(\App\Services\DataImportService::class)->reprocessAll();
        $this->assertContains('HOLD', $e->fresh()->signalList());
    }
}
