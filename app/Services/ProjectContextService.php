<?php
namespace App\Services;

use App\Models\{DataValidationError, EvidenceRecord, ProductionRecord, Project, ProjectUpdate, Setting};
use Illuminate\Support\Collection;

/** Assembles ProjectContext objects generically via project_id relations (BR-001). */
class ProjectContextService
{
    public function forProject(Project|int $project): ProjectContext
    {
        $id = $project instanceof Project ? $project->id : $project;
        return $this->forProjects([$id])[$id];
    }

    /** @return array<int,ProjectContext> keyed by project id */
    public function forProjects(?array $ids = null): array
    {
        $projects = Project::when($ids, fn ($q) => $q->whereIn('id', $ids))->orderBy('project_code')->get()->keyBy('id');
        $ids = $projects->keys()->all();
        $by = fn (Collection $c) => $c->groupBy('project_id');
        $updates = $by(ProjectUpdate::whereIn('project_id', $ids)->where('is_current', true)->orderBy('occurred_at')->orderBy('id')->get());
        $prod = $by(ProductionRecord::whereIn('project_id', $ids)->where('is_current', true)->orderBy('observed_at')->orderBy('id')->get());
        $evi = $by(EvidenceRecord::whereIn('project_id', $ids)->orderBy('observed_at')->orderBy('id')->get());
        $asOf = Setting::asOf();
        $out = [];
        foreach ($projects as $id => $p) {
            $out[$id] = new ProjectContext($p, $updates->get($id, collect()), $prod->get($id, collect()), $evi->get($id, collect()), $asOf, collect());
        }
        return $out;
    }
}
