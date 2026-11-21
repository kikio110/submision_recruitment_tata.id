<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class StaleEvidenceRule extends Rule {
    public function key(): string { return 'stale_evidence'; }
    public function evaluate(ProjectContext $c): array {
        if ($c->isCompleted()) return [];
        $newest = $c->newestEvidenceAt();
        if (! $newest) return [];
        $hours = (int) $newest->diffInHours($c->asOf, true);
        if ($hours <= $this->th('stale_hours')) return [];
        $latest = $c->evidence->where('is_current', true)->sortByDesc('observed_at')->take(3)->all();
        $days = round($hours / 24, 1);
        return [$this->draft('project', 'STALE_EVIDENCE', $hours > 168 ? 'HIGH' : 'MEDIUM', 'Evidence terbaru sudah usang',
            "Evidence terbaru proyek berasal dari {$this->fmt($newest)} ($days hari sebelum tanggal acuan {$this->fmt($c->asOf)}). Progress yang dilaporkan tidak dapat dikonfirmasi kondisi saat ini; ini adalah ketidakpastian, bukan tanda aman.",
            $latest, ['*'], $newest)];
    }
}
