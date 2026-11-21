<?php
namespace Tests\Feature;

use App\Models\{DataImportBatch, FollowUp};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class PagesSmokeTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    public function test_all_six_projects_appear_on_dashboard_with_database_driven_kpis(): void
    {
        $this->seedDataset();
        $r = $this->get('/dashboard')->assertOk();
        foreach (['PRJ-001', 'PRJ-002', 'PRJ-003', 'PRJ-004', 'PRJ-005', 'PRJ-006'] as $c) $r->assertSee($c);
        $r->assertSee('High attention')->assertSee('Follow-up overdue');
    }

    public function test_every_page_renders(): void
    {
        $this->seedDataset();
        $f = $this->followUpFor($this->finding('PRJ-002', 'operational_status', 'scanner_status:SCN-B'));
        $p = $this->project('PRJ-002');
        $urls = ['/', '/projects', "/projects/{$p->id}", "/projects/{$p->id}/attention", '/attention', '/follow-ups', '/follow-ups/create', "/follow-ups/{$f->id}",
            '/data-management', '/data-management/imports', '/data-management/imports/' . DataImportBatch::first()->id, '/activity', '/settings'];
        foreach ($urls as $u) $this->get($u)->assertStatus($u === '/' ? 302 : 200);
        foreach ($this->project('PRJ-002')->updates as $u) $this->get("/projects/{$p->id}")->assertSee($u->update_code);
    }

    public function test_project_detail_shows_source_references_and_reported_vs_observed(): void
    {
        $this->seedDataset();
        $p = $this->project('PRJ-005');
        $this->get("/projects/{$p->id}")->assertOk()->assertSee('Operational evidence')->assertSee('NOT_CONFIGURED')->assertSee('UPD-016')->assertSee('API-EPS-002')->assertSee('Dilaporkan');
    }
}
