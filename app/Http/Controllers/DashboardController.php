<?php
namespace App\Http\Controllers;

use App\Models\{AttentionAssessment, Finding, FollowUp, Project, Setting};
use App\Services\ProjectContextService;

class DashboardController extends Controller
{
    public function __invoke(ProjectContextService $contexts)
    {
        $asOf = Setting::asOf();
        $levels = AttentionAssessment::where('is_current', true)->selectRaw('level, count(*) c')->groupBy('level')->pluck('c', 'level');
        $fuStatus = FollowUp::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');
        $open = FollowUp::whereIn('status', ['OPEN', 'IN_PROGRESS']);
        $kpi = [
            'total' => Project::count(), 'high' => (int) ($levels['HIGH'] ?? 0), 'medium' => (int) ($levels['MEDIUM'] ?? 0), 'low' => (int) ($levels['LOW'] ?? 0),
            'verification' => Project::whereHas('findings', fn ($q) => $q->whereIn('verification_status', ['PENDING_VERIFICATION', 'UNVERIFIED', 'REOPENED']))->count(),
            'open_followups' => (clone $open)->count(), 'overdue' => (clone $open)->whereDate('due_date', '<', $asOf->toDateString())->count(),
        ];
        $progress = collect($contexts->forProjects())->map(fn ($c) => ['code' => $c->project->project_code, 'reported' => $c->project->reported_progress_pct,
            'observed' => ($o = $c->observedProgress()) ? $o['pct'] : null])->values();
        return view('pages.dashboard', ['kpi' => $kpi, 'progress' => $progress, 'fuStatus' => collect(FollowUp::STATUSES)->mapWithKeys(fn ($s) => [$s => (int) ($fuStatus[$s] ?? 0)]), 'asOf' => $asOf, 'pendingEvidence' => \App\Services\EvidenceSubmissionService::pendingCount()]);
    }
}
