<?php
namespace App\Services;

use App\Models\{AttentionAssessment, Finding};
use Illuminate\Support\Collection;

/**
 * Transparent weighted attention score (SDD decision; SRS section 9 - not AHP/SAW).
 * Score = management priority, NOT probability of failure. Criteria without data are UNKNOWN,
 * excluded from the mean and lower confidence (BR-015) instead of counting as zero risk.
 */
class AttentionService
{
    private const GROUPS = [
        'operational_blockage' => ['OPERATIONAL_BLOCKAGE', 'MISSING_PREREQUISITE'],
        'progress_deviation' => ['SCHEDULE_DEVIATION', 'PROGRESS_DISCREPANCY'],
        'evidence_reliability' => ['STALE_EVIDENCE', 'CONFLICTING_EVIDENCE', 'INSUFFICIENT_EVIDENCE'],
        'data_quality' => ['DATA_QUALITY'],
    ];
    public const LABELS = ['deadline_pressure' => 'Deadline Pressure', 'operational_blockage' => 'Operational Blockage',
        'progress_deviation' => 'Progress Deviation', 'evidence_reliability' => 'Evidence Reliability', 'data_quality' => 'Data Quality Issues'];

    public function assess(ProjectContext $ctx, Collection $findings, ?int $batchId = null): AttentionAssessment
    {
        $w = config('ontrack.weights'); $pts = config('ontrack.severity_points');
        $active = $findings->where('is_active', true);
        $criteria = [];
        $criteria['deadline_pressure'] = $this->deadline($ctx);
        foreach (self::GROUPS as $k => $cats) {
            $fs = $active->whereIn('category', $cats)->where('severity', '!=', 'INFO');
            $known = $this->isKnown($k, $ctx);
            if (! $known) { $criteria[$k] = ['score' => null, 'reason' => 'Tidak ada data untuk menilai kriteria ini (UNKNOWN, tidak dihitung sebagai aman).', 'finding_ids' => []]; continue; }
            if ($fs->isEmpty()) { $criteria[$k] = ['score' => 0.0, 'reason' => 'Tidak ada temuan aktif pada kriteria ini berdasarkan data yang tersedia.', 'finding_ids' => []]; continue; }
            $scores = $fs->map(fn ($f) => $pts[$f->severity])->sort()->reverse()->values();
            $score = min(100, $scores->first() + 0.25 * $scores->slice(1)->sum());
            $criteria[$k] = ['score' => (float) $score, 'finding_ids' => $fs->pluck('id')->all(),
                'reason' => $fs->map(fn ($f) => "[{$f->severity}] {$f->title}")->implode('; ')];
        }
        $knownW = 0; $sum = 0;
        foreach ($criteria as $k => &$c) {
            $c['key'] = $k; $c['label'] = self::LABELS[$k]; $c['weight'] = $w[$k]; $c['status'] = $c['score'] === null ? 'UNKNOWN' : 'KNOWN';
            if ($c['score'] !== null) { $knownW += $w[$k]; $sum += $w[$k] * $c['score']; $c['contribution'] = round($w[$k] * $c['score'] / max($knownW, 0.0001), 1); }
        }
        unset($c);
        $score = $knownW > 0 ? round($sum / $knownW, 1) : null;
        $confidence = round($knownW / array_sum($w), 2);
        foreach ($criteria as $k => &$c) if ($c['score'] !== null) $c['contribution'] = round($w[$k] * $c['score'] / $knownW, 1);
        unset($c);
        $level = $score === null ? 'MEDIUM' : ($score >= config('ontrack.levels.HIGH') ? 'HIGH' : ($score >= config('ontrack.levels.MEDIUM') ? 'MEDIUM' : 'LOW'));
        $notes = [];
        if ($active->contains('severity', 'CRITICAL') && $level !== 'HIGH') { $level = 'HIGH'; $notes[] = 'Level dinaikkan ke HIGH karena ada temuan CRITICAL (aturan lantai).'; }
        if ($active->whereIn('severity', ['HIGH', 'MEDIUM'])->isNotEmpty() && $level === 'LOW') { $level = 'MEDIUM'; $notes[] = 'Level dinaikkan ke MEDIUM karena ada temuan aktif berseverity MEDIUM atau lebih tinggi (aturan lantai).'; }
        if ($confidence < 0.6 && $level === 'LOW') { $level = 'MEDIUM'; $notes[] = 'Level tidak boleh LOW saat keyakinan data rendah (' . $confidence . ').'; }

        $assessment = AttentionAssessment::create(['project_id' => $ctx->project->id, 'score' => $score, 'level' => $level, 'confidence' => $confidence,
            'criteria' => array_values($criteria), 'explanation' => $this->explain($ctx, $criteria, $active, $score, $level, $confidence, $notes),
            'as_of' => $ctx->asOf, 'is_current' => true, 'source_batch_id' => $batchId]);
        AttentionAssessment::where('project_id', $ctx->project->id)->where('id', '!=', $assessment->id)->update(['is_current' => false]);
        return $assessment;
    }

    private function isKnown(string $k, ProjectContext $ctx): bool
    {
        return match ($k) {
            'progress_deviation' => $ctx->effectiveProgress()['pct'] !== null || $ctx->operational()->isNotEmpty(),
            default => $ctx->evidence->isNotEmpty(),
        };
    }

    /** Remaining work vs remaining time. Completed projects carry no deadline pressure. */
    private function deadline(ProjectContext $ctx): array
    {
        $unknown = ['score' => null, 'finding_ids' => [], 'reason' => 'Deadline atau progress tidak tersedia (UNKNOWN).'];
        if ($ctx->isCompleted()) return ['score' => 0.0, 'finding_ids' => [], 'reason' => 'Proyek dilaporkan COMPLETED; tidak ada tekanan deadline.'];
        $eff = $ctx->effectiveProgress(); $el = $ctx->elapsedPct(); $left = $ctx->daysLeft();
        if ($eff['pct'] === null || $el === null) return $unknown;
        if ($left < 0 && $eff['pct'] < 100) return ['score' => 100.0, 'finding_ids' => [], 'reason' => 'Deadline sudah terlewati ' . abs($left) . ' hari dengan progress ' . $eff['pct'] . '%.'];
        $remainWork = 100 - $eff['pct']; $remainTime = max(100 - $el, 1);
        $ratio = $remainWork / $remainTime;
        $score = max(0, min(100, ($ratio - 0.7) / 1.3 * 100));
        return ['score' => round($score, 1), 'finding_ids' => [],
            'reason' => "Sisa pekerjaan {$remainWork}% vs sisa waktu " . round($remainTime, 1) . "% (sisa $left hari); rasio " . round($ratio, 2) . " memakai progress {$eff['label']} ({$eff['basis']})."];
    }

    private function explain(ProjectContext $ctx, array $criteria, Collection $active, ?float $score, string $level, float $conf, array $notes): string
    {
        $parts = [];
        $parts[] = "Prioritas perhatian $level" . ($score !== null ? " (skor $score/100)" : ' (skor tidak dapat dihitung)') . ', keyakinan data ' . round($conf * 100) . '%. Skor menggambarkan prioritas manajemen, bukan probabilitas proyek gagal.';
        $top = collect($criteria)->filter(fn ($c) => $c['score'] !== null && $c['score'] > 0)->sortByDesc('contribution')->take(3);
        if ($top->isNotEmpty()) $parts[] = 'Kontribusi terbesar: ' . $top->map(fn ($c) => "{$c['label']} ({$c['contribution']} poin)")->implode(', ') . '.';
        $unknown = collect($criteria)->where('status', 'UNKNOWN')->pluck('label');
        if ($unknown->isNotEmpty()) $parts[] = 'Kriteria UNKNOWN (kurang data): ' . $unknown->implode(', ') . '.';
        $f = $active->where('severity', '!=', 'INFO')->sortBy(fn ($x) => array_search($x->severity, ['CRITICAL', 'HIGH', 'MEDIUM', 'LOW']));
        $parts[] = $f->isEmpty() ? 'Tidak ada temuan aktif; ini berarti tidak ada kondisi bermasalah yang terdeteksi dari data yang ada, bukan jaminan proyek aman.'
            : 'Temuan utama: ' . $f->take(4)->map(fn ($x) => "[{$x->severity}] {$x->title}")->implode('; ') . '.';
        return implode(' ', array_merge($parts, $notes));
    }
}
