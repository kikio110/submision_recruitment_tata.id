<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class ReportedBlockerRule extends Rule {
    public function key(): string { return 'reported_blocker'; }
    public function evaluate(ProjectContext $c): array {
        $since = $c->asOf->subDays($this->th('reported_issue_days'));
        $msgs = $c->messages()->sortBy('observed_at')->values();
        $recent = $msgs->filter(fn ($m) => $m->observed_at->gte($since));
        $issues = $recent->filter(fn ($m) => array_intersect(['BLOCKER', 'WAITING', 'HOLD', 'QUALITY'], $m->signalList()) && ! in_array('RECOVERY', $m->signalList()));
        if ($issues->isEmpty()) return [];
        $lastIssue = $issues->last();
        $recovered = $msgs->filter(fn ($m) => $m->observed_at->gt($lastIssue->observed_at) && in_array('RECOVERY', $m->signalList()))->isNotEmpty();
        if ($recovered) return [];
        $opBad = $c->currentOperational()->contains(fn ($e) => in_array($e->metric_key, ['scanner_status', 'connection_status', 'application_status']) && ! in_array($e->observed_value, config("ontrack.healthy.{$e->metric_key}", []), true));
        $quality = $issues->every(fn ($m) => in_array('QUALITY', $m->signalList()) && ! array_intersect(['BLOCKER', 'WAITING', 'HOLD'], $m->signalList()));
        $conf = $opBad ? 'Dikuatkan oleh kondisi operasional tidak sehat pada evidence operasional.' : 'Belum dikuatkan evidence operasional; ini masih laporan manusia.';
        return [$this->draft('reported_issue', 'OPERATIONAL_BLOCKAGE', $quality ? 'LOW' : 'MEDIUM', 'Kendala dilaporkan tim lapangan/PM',
            'Update terbaru melaporkan kendala atau penahanan pekerjaan (' . $issues->pluck('source_ref')->implode(', ') . "). $conf",
            $issues->take(-4)->map(fn ($m) => [$m, 'SUPPORTS'])->all(), ['*'])];
    }
}
