<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class ProgressDiscrepancyRule extends Rule {
    public function key(): string { return 'progress_discrepancy'; }
    public function evaluate(ProjectContext $c): array {
        $rep = $c->latest('reported_progress_pct', null, 'REPORTED'); $o = $c->observedProgress();
        if (! $rep || ! $o) return [];
        $diff = $rep->numeric_value - $o['pct'];
        if (abs($diff) <= $this->th('progress_tolerance_pct')) return [];
        // reported actual = verified + pending -> explained by pending quantity rule
        $pend = $c->latest('pending_internal_check_quantity', null, 'OPERATIONAL'); $ver = $c->latest('verified_quantity', null, 'OPERATIONAL'); $act = $c->latest('reported_actual', null, 'REPORTED');
        if ($pend && $ver && $act && abs($act->numeric_value - ($pend->numeric_value + $ver->numeric_value)) < 0.5) return [];
        $a = abs($diff); $sev = $a > 30 ? 'HIGH' : ($a > 10 ? 'MEDIUM' : 'LOW');
        $gapH = round($rep->observed_at->diffInHours($o['evidence']->observed_at, true), 1);
        $note = '';
        if ($gapH > $this->th('time_gap_hours')) { $sev = $this->lower($sev); $note = " Waktu observasi berbeda $gapH jam, sehingga selisih belum tentu kesalahan (keyakinan lebih rendah)."; }
        $stages = '';
        if ($o['stages']) $stages = ' Tahapan: ' . collect($o['stages'])->map(fn ($s) => "{$s['label']} {$s['value']} ({$s['pct']}%)")->implode('; ') . '.';
        $dir = $diff > 0 ? 'lebih tinggi' : 'lebih rendah';
        return [$this->draft('reported_vs_observed', 'PROGRESS_DISCREPANCY', $sev, 'Progress dilaporkan berbeda dari evidence operasional',
            "Project master melaporkan {$rep->numeric_value}% ({$this->fmt($rep->observed_at)}), $dir daripada progress operasional {$o['pct']}% berdasarkan {$o['label']} ({$this->fmt($o['evidence']->observed_at)}).$stages$note",
            [$o['evidence'], [$rep, 'CONTRAST']], [$o['evidence']->metric_key])];
    }
}
