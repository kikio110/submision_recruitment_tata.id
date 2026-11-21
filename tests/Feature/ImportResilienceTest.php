<?php
namespace Tests\Feature;

use App\Models\{DataImportBatch, DataValidationError, EvidenceRecord, Finding, FollowUp, FollowUpHistory, ProductionRecord, Project, ProjectUpdate};
use App\Services\DataImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class ImportResilienceTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    private function counts(): array
    {
        return [Project::count(), ProjectUpdate::count(), ProductionRecord::count(), EvidenceRecord::count(), Finding::count()];
    }

    public function test_reimporting_same_files_does_not_duplicate_source_records(): void
    {
        $this->seedDataset();
        $before = $this->counts();
        $dir = database_path('data');
        $batches = app(DataImportService::class)->importFiles([['path' => "$dir/02_Project_Master.csv", 'name' => 'a.csv'], ['path' => "$dir/03_Project_Updates.csv", 'name' => 'b.csv'], ['path' => "$dir/04_Production_Data.json", 'name' => 'c.json']]);
        $this->assertSame($before, $this->counts());
        $this->assertSame([6, 23, 12], array_map(fn ($b) => $b->duplicate_records, $batches));
        $this->assertSame([0, 0, 0], array_map(fn ($b) => $b->new_records, $batches));
        $this->assertSame([6, 23, 12], [Project::count(), ProjectUpdate::count(), ProductionRecord::count()]);
    }

    public function test_data_refresh_preserves_follow_ups_and_their_history(): void
    {
        $this->seedDataset();
        $fu = $this->followUpFor($this->finding('PRJ-005', 'missing_prerequisite'), 'Raka');
        app(\App\Services\FollowUpService::class)->addNote($fu, 'Eskalasi dikirim');
        $histBefore = FollowUpHistory::where('follow_up_id', $fu->id)->count();
        $this->import('prod2.json', $this->prodJson([[ 'source_record_id' => 'API-EPS-003', 'project_id' => 'PRJ-005', 'source_system' => 'integration_monitor', 'record_type' => 'integration_snapshot',
            'timestamp' => '2026-10-07T10:00:00', 'developed_endpoints' => 9, 'configured_endpoints' => 9, 'production_tested_endpoints' => 2, 'target_endpoints' => 10]]), '2026-10-07 12:00:00');
        $dir = database_path('data');
        app(DataImportService::class)->importFiles([['path' => "$dir/02_Project_Master.csv", 'name' => 'a.csv']]);
        $fu2 = FollowUp::find($fu->id);
        $this->assertNotNull($fu2);
        $this->assertSame('Raka', $fu2->assigned_pic);
        $this->assertGreaterThanOrEqual($histBefore, FollowUpHistory::where('follow_up_id', $fu->id)->count());
        $this->assertContains('NOTE_ADDED', FollowUpHistory::where('follow_up_id', $fu->id)->pluck('event')->all());
        $this->assertNotNull($fu2->finding_id, 'finding identity (and its follow-up link) survives re-interpretation');
    }

    public function test_conflicting_production_evidence_is_flagged_and_both_values_preserved(): void
    {
        $this->seedDataset();
        $row = fn ($id, $n) => ['source_record_id' => $id, 'project_id' => 'PRJ-004', 'source_system' => $n, 'record_type' => 'production_snapshot', 'timestamp' => '2026-10-06T20:00:00', 'records_verified' => $id === 'X-1' ? 45000 : 47000, 'unit' => 'records'];
        $this->import('conf.json', $this->prodJson([$row('X-1', 'verifier_a'), $row('X-2', 'verifier_b')]), '2026-10-06 23:59:59');
        $ev = EvidenceRecord::where('metric_key', 'records_verified')->whereIn('source_ref', ['X-1', 'X-2'])->whereNotNull('conflict_group')->get();
        $this->assertSame(2, $ev->count());
        $this->assertEqualsCanonicalizing(['45000', '47000'], $ev->pluck('observed_value')->all());
        $this->assertTrue($this->finding('PRJ-004', 'conflicting_evidence') !== null);
    }

    public function test_changed_record_with_same_id_becomes_a_flagged_revision_not_an_overwrite(): void
    {
        $this->seedDataset();
        $before = ProductionRecord::where('source_record_id', 'SCAN-BETA-001')->count();
        $b = $this->import('rev.json', $this->prodJson([[ 'source_record_id' => 'SCAN-BETA-001', 'project_id' => 'PRJ-002', 'source_system' => 'scanner_production_log', 'record_type' => 'production_snapshot',
            'timestamp' => '2026-10-05T23:59:00', 'pages_processed_total' => 88000, 'unit' => 'pages']]));
        $this->assertSame(1, $b->conflicting_records);
        $this->assertSame($before + 1, ProductionRecord::where('source_record_id', 'SCAN-BETA-001')->count());
        $this->assertSame(1, ProductionRecord::where('source_record_id', 'SCAN-BETA-001')->where('is_current', false)->count());
        $this->assertTrue(DataValidationError::where('batch_id', $b->id)->where('code', 'REVISED_RECORD')->exists());
    }

    public function test_invalid_and_missing_records_are_reported_never_silently_dropped(): void
    {
        $this->seedDataset();
        $csv = "project_id,customer,project_name,project_type,pic,start_date,deadline,target,target_unit,reported_actual,reported_progress_pct,reported_status,last_reported_update_at\n"
            . "PRJ-070,A,Valid,T,Z,2026-09-01,2026-10-30,100,u,10,10,ON TRACK,2026-10-06T10:00:00\n"
            . "PRJ-071,A,Bad progress,T,Z,2026-09-01,2026-10-30,100,u,10,150,ON TRACK,2026-10-06T10:00:00\n"
            . ",A,No id,T,Z,2026-09-01,2026-10-30,100,u,10,10,ON TRACK,2026-10-06T10:00:00\n"
            . "PRJ-072,A,Bad date,T,Z,01/09/2026,2026-10-30,100,u,10,10,ON TRACK,2026-10-06T10:00:00\n"
            . "PRJ-073,A,Missing pic,T,,2026-09-01,2026-10-30,100,u,,,,\n";
        $b = $this->import('bad.csv', $csv);
        $this->assertSame(5, $b->total_records);
        $this->assertSame(2, $b->valid_records);
        $this->assertSame(3, $b->invalid_records);
        $this->assertSame('COMPLETED_WITH_WARNINGS', $b->status);
        $this->assertSame(['PRJ-070', 'PRJ-073'], Project::whereIn('project_code', ['PRJ-070', 'PRJ-071', 'PRJ-072', 'PRJ-073'])->orderBy('project_code')->pluck('project_code')->all());
        $this->assertGreaterThanOrEqual(3, $b->errors()->where('severity', 'ERROR')->count());
        $this->assertTrue($b->errors()->where('code', 'MISSING_OPTIONAL')->exists());
    }

    public function test_unknown_structure_and_oversize_files_are_rejected_without_touching_data(): void
    {
        $this->seedDataset();
        $before = $this->counts();
        $b = $this->import('x.csv', "foo,bar\n1,2\n");
        $this->assertSame('REJECTED', $b->status);
        $b2 = $this->import('x.json', '{not json');
        $this->assertSame('REJECTED', $b2->status);
        $b3 = $this->import('x.exe', 'MZ');
        $this->assertSame('REJECTED', $b3->status);
        $this->assertSame($before, $this->counts());
    }

    public function test_failure_during_persistence_rolls_back_the_whole_batch(): void
    {
        $this->seedDataset();
        $before = $this->counts();
        $svc = new class(app(\App\Services\DataValidationService::class), app(\App\Services\EvidenceService::class), app(\App\Services\ProjectContextService::class),
            app(\App\Services\InterpretationService::class), app(\App\Services\AttentionService::class), app(\App\Services\IssueVerificationService::class)) extends DataImportService {
            public function postProcess(array $projectIds, ?DataImportBatch $batch = null): void { throw new \RuntimeException('simulated failure'); }
        };
        $b = $svc->importFile($this->tmp('ok.json', $this->prodJson([[ 'source_record_id' => 'NEW-1', 'project_id' => 'PRJ-001', 'source_system' => 's', 'record_type' => 'production_snapshot',
            'timestamp' => '2026-10-07T01:00:00', 'verified_quantity' => 999, 'unit' => 'ML']])), 'ok.json');
        $this->assertSame('FAILED', $b->status);
        $this->assertStringContainsString('rollback', $b->message);
        $this->assertSame($before, $this->counts());
        $this->assertFalse(ProductionRecord::where('source_record_id', 'NEW-1')->exists());
    }

    public function test_missing_operational_evidence_is_uncertainty_not_success(): void
    {
        $this->seedDataset();
        $this->import('m.csv', "project_id,customer,project_name,project_type,pic,start_date,deadline,target,target_unit,reported_actual,reported_progress_pct,reported_status,last_reported_update_at\nPRJ-080,K,Tanpa evidence,T,Zed,2026-09-20,2026-10-30,100,records,50,50.0,ON TRACK,2026-10-06T10:00:00\n");
        $p = $this->project('PRJ-080');
        $f = $this->finding('PRJ-080', 'insufficient_evidence');
        $this->assertSame('INSUFFICIENT_EVIDENCE', $f->category);
        $fu = $this->followUpFor($f);
        app(\App\Services\FollowUpService::class)->changeStatus($fu, 'RESOLVED');
        $this->assertNotSame('VERIFIED', $f->fresh()->verification_status);
        $this->assertSame('UNVERIFIED', $f->fresh()->verification_status);
        $criteria = collect($p->currentAssessment->criteria)->keyBy('key');
        $this->assertNotSame(0.0, $criteria['evidence_reliability']['score']);
        $this->assertNotSame('LOW', $p->currentAssessment->level);
    }

    public function test_batch_history_and_activity_are_recorded(): void
    {
        $this->seedDataset();
        $this->assertSame(3, DataImportBatch::count());
        $this->assertTrue(\App\Models\ActivityLog::where('type', 'DATA_IMPORT')->count() >= 3);
    }
}
