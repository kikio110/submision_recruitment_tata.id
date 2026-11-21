<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class ScheduleDeviationRule extends Rule {
    public function key(): string { return 'schedule_deviation'; }
    public function evaluate(ProjectContext $c): array {
        if ($c->isCompleted()) return [];
        $elapsed = $c->elapsedPct(); $eff = $c->effectiveProgress(); $left = $c->daysLeft();
        if ($elapsed === null || $eff['pct'] === null) return [];
        $o = $c->observedProgress();
        $ev = $o ? [$o['evidence']] : array_filter([$c->latest('reported_progress_pct')]);
        $metrics = $o ? [$o['evidence']->metric_key] : ['reported_progress_pct'];
        $basis = $eff['basis'] === 'OPERATIONAL' ? "evidence operasional ({$eff['label']})" : 'progress yang dilaporkan';
        if ($left < 0 && $eff['pct'] < 100) {
            return [$this->draft('deadline', 'SCHEDULE_DEVIATION', 'CRITICAL', 'Deadline terlewati sebelum progress mencapai 100%',
                "Deadline {$c->project->deadline->format('d M Y')} sudah lewat " . abs($left) . " hari pada tanggal acuan {$this->fmt($c->asOf)}, sementara progress berdasarkan $basis baru {$eff['pct']}%.", $ev, $metrics)];
        }
        $gap = $elapsed - $eff['pct'];
        if ($gap < $this->th('schedule_gap_pct')) return [];
        $sev = $gap >= 40 ? 'CRITICAL' : ($gap >= 20 ? 'HIGH' : ($gap >= 10 ? 'MEDIUM' : 'LOW'));
        return [$this->draft('schedule', 'SCHEDULE_DEVIATION', $sev, 'Progress tertinggal dari jadwal',
            "Waktu terpakai {$elapsed}% ({$c->project->start_date->format('d M')} - {$c->project->deadline->format('d M Y')}, sisa $left hari) sedangkan progress berdasarkan $basis {$eff['pct']}%. Selisih " . round($gap, 1) . ' poin.', $ev, $metrics)];
    }
}
