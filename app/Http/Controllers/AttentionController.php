<?php
namespace App\Http\Controllers;

use App\Models\{AttentionAssessment, Project};
use App\Services\ProjectContextService;

class AttentionController extends Controller
{
    public function index()
    {
        $items = AttentionAssessment::where('is_current', true)->with('project')->orderByDesc('score')->get();
        return view('pages.attention.index', ['items' => $items]);
    }

    public function show(Project $project, ProjectContextService $contexts)
    {
        $a = $project->currentAssessment;
        $findings = $project->findings()->where('is_active', true)->with('evidence')->orderByRaw("field(severity,'CRITICAL','HIGH','MEDIUM','LOW','INFO')")->get();
        $history = $project->assessments()->orderByDesc('id')->limit(8)->get();
        return view('pages.attention.show', ['project' => $project, 'assessment' => $a, 'findings' => $findings, 'history' => $history, 'ctx' => $contexts->forProject($project)]);
    }
}
