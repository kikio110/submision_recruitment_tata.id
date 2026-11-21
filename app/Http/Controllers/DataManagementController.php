<?php
namespace App\Http\Controllers;

use App\Models\{DataImportBatch, Project, Setting};
use App\Services\DataImportService;
use App\Support\CurrentUser;

class DataManagementController extends Controller
{
    public function index()
    {
        return view('pages.data.index', ['recent' => DataImportBatch::latest('id')->limit(5)->get(), 'counts' => [
            'projects' => Project::count(), 'updates' => \App\Models\ProjectUpdate::where('is_current', true)->count(),
            'production' => \App\Models\ProductionRecord::where('is_current', true)->count(), 'evidence' => \App\Models\EvidenceRecord::count()], 'asOf' => Setting::asOf()]);
    }

    public function imports() { return view('pages.data.imports', ['batches' => DataImportBatch::with('creator')->latest('id')->paginate(15)]); }

    public function show(DataImportBatch $batch)
    {
        $issues = $batch->errors()->orderByRaw("field(severity,'ERROR','WARNING','INFO')")->orderBy('id')->paginate(50);
        return view('pages.data.show', compact('batch', 'issues'));
    }

    public function reprocess(DataImportService $svc)
    {
        abort_unless(CurrentUser::get()?->isDirector(), 403);
        $svc->reprocessAll();
        \App\Services\ActivityLogService::log('REPROCESS', 'Interpretasi dan attention dihitung ulang untuk semua proyek.');
        return back()->with('success', 'Interpretasi, attention, dan verifikasi dihitung ulang dari data tersimpan.');
    }
}
