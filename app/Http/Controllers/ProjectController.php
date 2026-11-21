<?php
namespace App\Http\Controllers;

use App\Http\Requests\{ProjectFormRequest, ProjectReportRequest};
use App\Models\Project;
use App\Services\{ActivityLogService, DataImportService, DataValidationService, ProjectContextService, ProjectReportService};
use App\Support\CurrentUser;

class ProjectController extends Controller
{
    public function index() { return view('pages.projects.index'); }

    public function show(Project $project, ProjectContextService $contexts)
    {
        $ctx = $contexts->forProject($project);
        $project->load(['currentAssessment', 'followUps.finding']);
        $findings = $project->findings()->with('evidence')->orderByRaw("field(severity,'CRITICAL','HIGH','MEDIUM','LOW','INFO')")->orderByDesc('is_active')->get();
        // Timeline: reported updates + operational observations merged chronologically (BR-002)
        $timeline = $ctx->evidence->filter(fn ($e) => $e->metric_key === 'update_message' || $e->evidence_class === 'OPERATIONAL')->sortBy([['observed_at', 'asc'], ['id', 'asc']])->values();
        return view('pages.projects.show', ['project' => $project, 'ctx' => $ctx, 'assessment' => $project->currentAssessment, 'findings' => $findings,
            'timeline' => $timeline, 'masterEvidence' => $ctx->evidence->where('origin_table', 'projects')->sortByDesc('observed_at'), 'observed' => $ctx->observedProgress()]);
    }

    public function create()
    {
        abort_unless(CurrentUser::get()?->isDirector(), 403, 'Hanya Director yang dapat menambah proyek.');
        return view('pages.projects.form', ['project' => null, 'statuses' => self::STATUSES]);
    }

    public function store(ProjectFormRequest $r, DataImportService $import)
    {
        $b = $import->importRows(DataValidationService::KIND_MASTER, [$this->row($r->validated())], 'Input manual: tambah proyek ' . strtoupper($r->project_id));
        if ($b->invalid_records > 0 || $b->status === 'FAILED') return back()->withInput()->withErrors(['project_id' => $b->errors()->where('severity', 'ERROR')->first()?->message ?? $b->message]);
        $p = Project::where('project_code', strtoupper($r->project_id))->firstOrFail();
        ActivityLogService::log('PROJECT_CREATED', "Proyek {$p->project_code} \"{$p->name}\" ditambahkan secara manual.", $p->id, null, $b->id);
        return redirect()->route('projects.show', $p)->with('success', 'Proyek ditambahkan. Temuan dan attention sudah dihitung dari data yang tersedia.');
    }

    public function edit(Project $project)
    {
        abort_unless(CurrentUser::get()?->isDirector(), 403, 'Hanya Director yang dapat mengubah proyek.');
        return view('pages.projects.form', ['project' => $project, 'statuses' => self::STATUSES]);
    }

    public function update(ProjectFormRequest $r, Project $project, DataImportService $import)
    {
        $row = $this->row($r->validated() + ['project_id' => $project->project_code]);
        if (($row['last_reported_update_at'] ?? '') === '') $row['last_reported_update_at'] = $project->last_reported_update_at?->format('Y-m-d\TH:i:s');
        $b = $import->importRows(DataValidationService::KIND_MASTER, [$row], "Input manual: ubah proyek {$project->project_code}");
        if ($b->invalid_records > 0 || $b->status === 'FAILED') return back()->withInput()->withErrors(['project_name' => $b->errors()->where('severity', 'ERROR')->first()?->message ?? $b->message]);
        if ($b->duplicate_records > 0) return redirect()->route('projects.show', $project)->with('success', 'Tidak ada perubahan data.');
        ActivityLogService::log('PROJECT_UPDATED', "Data proyek {$project->project_code} diubah; versi lama tetap tersimpan sebagai riwayat.", $project->id, null, $b->id);
        return redirect()->route('projects.show', $project)->with('success', 'Proyek diperbarui. Analisis dihitung ulang; lihat dampaknya di riwayat import.');
    }

    public function destroy(Project $project)
    {
        abort_unless(CurrentUser::get()?->isDirector(), 403);
        if ($project->followUps()->exists()) return back()->with('error', 'Proyek memiliki follow-up sehingga tidak dapat dihapus. Riwayat tindakan harus tetap terjaga.');
        $code = $project->project_code;
        $project->delete();
        ActivityLogService::log('PROJECT_DELETED', "Proyek $code dihapus oleh Director.");
        return redirect()->route('projects.index')->with('success', "Proyek $code dihapus.");
    }

    public function reportForm(Project $project, ProjectReportService $svc)
    {
        abort_unless($svc->canReport(CurrentUser::get(), $project), 403, 'Laporan hanya dapat dibuat oleh PIC proyek atau Director.');
        return view('pages.projects.report', ['project' => $project, 'sources' => ProjectReportService::SOURCES, 'defaultAt' => ProjectReportService::defaultReportTime(), 'statuses' => self::STATUSES]);
    }

    public function report(ProjectReportRequest $r, Project $project, ProjectReportService $svc)
    {
        $svc->report($project, $r->validated());
        return redirect()->route('projects.show', $project)->with('success', 'Laporan diterima dan muncul pada timeline sebagai informasi yang dilaporkan. Analisis telah dihitung ulang.');
    }

    private function row(array $d): array
    {
        $row = array_merge(['project_id' => '', 'project_name' => '', 'customer' => '', 'project_type' => '', 'pic' => '', 'start_date' => '', 'deadline' => '', 'target' => '', 'target_unit' => '',
            'reported_actual' => '', 'reported_progress_pct' => '', 'reported_status' => '', 'last_reported_update_at' => ''], array_map(fn ($v) => $v ?? '', $d));
        if ($row['last_reported_update_at'] !== '') $row['last_reported_update_at'] = \Carbon\CarbonImmutable::parse($row['last_reported_update_at'])->format('Y-m-d\TH:i:s');
        return $row;
    }

    private const STATUSES = ['ON TRACK', 'AT RISK', 'DELAYED', 'ON HOLD', 'COMPLETED'];
}
