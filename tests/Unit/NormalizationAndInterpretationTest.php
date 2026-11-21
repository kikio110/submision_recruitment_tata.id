<?php
namespace Tests\Unit;

use App\Models\Finding;
use App\Services\DataValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class NormalizationAndInterpretationTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    public function test_master_row_is_normalised_and_invalid_values_are_reported_not_coerced(): void
    {
        $v = new DataValidationService();
        [$ok, $e] = $v->master(['project_id' => ' prj-009 ', 'customer' => 'X', 'project_name' => 'Y', 'start_date' => '2026-09-01', 'deadline' => '2026-10-01',
            'target' => '10', 'reported_progress_pct' => '50', 'reported_status' => 'on track'], 'baris 2');
        $this->assertSame('PRJ-009', $ok['project_code']);
        $this->assertSame('ON TRACK', $ok['reported_status']);
        [$bad, $errs] = $v->master(['project_id' => 'P', 'customer' => 'X', 'project_name' => 'Y', 'start_date' => '2026-09-01', 'deadline' => '2026-08-01',
            'target' => '0', 'reported_progress_pct' => '140'], 'baris 3');
        $this->assertNull($bad);
        $codes = collect($errs)->pluck('code')->all();
        $this->assertContains('RANGE', $codes);
        $this->assertGreaterThanOrEqual(3, collect($errs)->where('code', 'RANGE')->count());
    }

    public function test_generic_rules_derive_expected_findings_from_supplied_dataset(): void
    {
        $this->seedDataset();
        $has = fn (string $code, string $cat) => Finding::where('project_id', $this->project($code)->id)->where('category', $cat)->where('is_active', true)->exists();
        // PRJ-001: verified vs pending quantity
        $this->assertTrue($has('PRJ-001', 'PROGRESS_DISCREPANCY'));
        // PRJ-002: current scanner state is LIMITED, OUT_OF_SERVICE retained in history
        $f = $this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B');
        $this->assertStringContainsString('LIMITED', $f->title);
        $this->assertStringContainsString('OUT_OF_SERVICE', $f->description);
        $this->assertSame(2, $f->evidence()->where('metric_key', 'scanner_status')->count());
        // PRJ-003: stale evidence; PRJ-004: data quality separate from progress mismatch
        $this->assertTrue($has('PRJ-003', 'STALE_EVIDENCE'));
        $this->assertTrue($has('PRJ-004', 'DATA_QUALITY'));
        $this->assertTrue($has('PRJ-004', 'PROGRESS_DISCREPANCY'));
        // PRJ-005: development vs production testing, missing prerequisite
        $this->assertTrue($has('PRJ-005', 'MISSING_PREREQUISITE'));
        $this->assertStringContainsString('Endpoint teruji di production', $this->finding('PRJ-005', 'progress_discrepancy')->description);
        // PRJ-006: single failed login is INFO, not a systemic outage
        $this->assertSame('INFO', $this->finding('PRJ-006', 'usage_health')->severity);
        $this->assertSame('LOW', $this->project('PRJ-006')->currentAssessment->level);
        $this->assertSame('HIGH', $this->project('PRJ-005')->currentAssessment->level);
    }

    public function test_every_finding_links_to_evidence_and_attention_is_explained(): void
    {
        $this->seedDataset();
        foreach (Finding::all() as $f) {
            $this->assertGreaterThan(0, $f->evidence()->count(), "Finding {$f->title} has no evidence");
            $this->assertNotNull($f->detected_at);
        }
        foreach (\App\Models\AttentionAssessment::where('is_current', true)->get() as $a) {
            $this->assertNotEmpty($a->explanation);
            $this->assertCount(5, $a->criteria);
        }
    }

    public function test_services_contain_no_project_specific_conditions(): void
    {
        $files = array_merge(glob(app_path('Services/*.php')), glob(app_path('Services/Interpretation/*.php')), glob(app_path('Services/Interpretation/Rules/*.php')));
        $this->assertNotEmpty($files);
        foreach ($files as $f) $this->assertDoesNotMatchRegularExpression('/PRJ-\d+/', file_get_contents($f), "Hard-coded project id in $f");
    }
}
