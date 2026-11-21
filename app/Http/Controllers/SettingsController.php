<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\{ActivityLogService, DataImportService};
use App\Support\CurrentUser;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index() { return view('pages.settings', ['asOf' => Setting::asOf(), 'thresholds' => config('ontrack.thresholds'), 'weights' => config('ontrack.weights'), 'levels' => config('ontrack.levels'), 'labels' => config('ontrack_decisions.threshold_labels'), 'decisions' => config('ontrack_decisions.decisions')]); }

    public function update(Request $r, DataImportService $svc)
    {
        abort_unless(CurrentUser::get()?->isDirector(), 403);
        $d = $r->validate(['as_of' => ['required', 'date']]);
        Setting::write('as_of', \Carbon\CarbonImmutable::parse($d['as_of'])->toDateTimeString());
        $svc->reprocessAll();
        ActivityLogService::log('SETTINGS', "Tanggal acuan (as-of) diubah ke {$d['as_of']}; interpretasi dihitung ulang.");
        return back()->with('success', 'Tanggal acuan disimpan dan seluruh interpretasi dihitung ulang.');
    }
}
