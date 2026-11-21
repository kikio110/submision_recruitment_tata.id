<?php
namespace Tests\Feature;

use App\Services\ProjectContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class ProjectContextTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    public function test_context_groups_records_by_project_id_and_sorts_chronologically(): void
    {
        $this->seedDataset();
        $ctx = app(ProjectContextService::class)->forProjects();
        $this->assertCount(6, $ctx);
        $beta = collect($ctx)->first(fn ($c) => $c->project->project_code === 'PRJ-002');
        $this->assertCount(6, $beta->updates);
        $this->assertTrue($beta->updates->every(fn ($u) => $u->project_id === $beta->project->id));
        $times = $beta->updates->pluck('occurred_at')->map->timestamp->all();
        $sorted = $times; sort($sorted);
        $this->assertSame($sorted, $times);
        $this->assertCount(3, $beta->production);
    }

    public function test_newly_imported_project_works_without_any_special_case(): void
    {
        $this->seedDataset();
        $this->import('m.csv', "project_id,customer,project_name,project_type,pic,start_date,deadline,target,target_unit,reported_actual,reported_progress_pct,reported_status,last_reported_update_at\nPRJ-099,Baru,Proyek Baru,Test,Zed,2026-09-20,2026-10-30,100,records,10,10.0,ON TRACK,2026-10-06T10:00:00\n");
        $ctx = app(ProjectContextService::class)->forProject($this->project('PRJ-099'));
        $this->assertSame('PRJ-099', $ctx->project->project_code);
        $this->assertNotNull($this->project('PRJ-099')->currentAssessment);
    }
}
