<?php
namespace App\Http\Controllers;

use App\Models\{ActivityLog, Project};
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __invoke(Request $r)
    {
        $q = ActivityLog::with(['project', 'followUp'])->latest('id')
            ->when($r->type, fn ($q, $t) => $q->where('type', $t))->when($r->project, fn ($q, $p) => $q->where('project_id', $p))
            ->when($r->q, fn ($q, $s) => $q->where('description', 'like', "%$s%"));
        return view('pages.activity', ['logs' => $q->paginate(25)->withQueryString(), 'types' => ActivityLog::distinct()->orderBy('type')->pluck('type'), 'projects' => Project::orderBy('project_code')->get(['id', 'project_code'])]);
    }
}
