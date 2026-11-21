<?php
namespace App\Services;

use App\Models\Project;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/** Unified, read-only view of one project's reported + operational information. */
class ProjectContext
{
    public function __construct(
        public Project $project, public Collection $updates, public Collection $production,
        public Collection $evidence, public CarbonImmutable $asOf, public Collection $validationErrors) {}

    public function operational(): Collection { return $this->evidence->where('evidence_class', 'OPERATIONAL'); }
    public function reported(): Collection { return $this->evidence->where('evidence_class', 'REPORTED'); }
    public function currentOperational(): Collection { return $this->operational()->where('is_current', true); }
    public function messages(): Collection { return $this->evidence->where('metric_key', 'update_message')->where('is_current', true); }

    /** Latest current evidence for a metric (optionally subject), any class unless given. */
    public function latest(string $metric, ?string $subject = null, ?string $class = null): ?\App\Models\EvidenceRecord
    {
        return $this->evidence->where('is_current', true)->where('metric_key', $metric)
            ->when($subject !== null, fn ($c) => $c->where('subject_key', $subject))
            ->when($class, fn ($c) => $c->where('evidence_class', $class))
            ->sortByDesc('observed_at')->first();
    }

    public function history(string $metric, ?string $subject = null): Collection
    {
        return $this->evidence->where('metric_key', $metric)->when($subject !== null, fn ($c) => $c->where('subject_key', $subject))->sortBy('observed_at')->values();
    }

    public function isCompleted(): bool { return in_array($this->project->reported_status, ['COMPLETED', 'DONE', 'CLOSED']); }

    public function elapsedPct(): ?float
    {
        $p = $this->project;
        if (! $p->start_date || ! $p->deadline) return null;
        $total = max(1, $p->start_date->diffInDays($p->deadline, true));
        $el = $p->start_date->diffInDays($this->asOf->startOfDay(), false);
        return round(max(0, min(100, $el / $total * 100)), 2);
    }

    public function daysLeft(): ?int
    {
        return $this->project->deadline ? (int) $this->asOf->startOfDay()->diffInDays($this->project->deadline, false) : null;
    }

    /**
     * Operationally observed progress (0-100) or null when no operational basis exists.
     * Quantity metrics: value/target. Stage metrics: the most downstream stage.
     * @return array{pct:float,label:string,evidence:\App\Models\EvidenceRecord,stages:array}|null
     */
    public function observedProgress(): ?array
    {
        $p = $this->project; $catalog = config('ontrack.metrics'); $best = null;
        foreach ($this->currentOperational() as $e) {
            $c = $catalog[$e->metric_key] ?? null;
            if (! $c || $e->numeric_value === null) continue;
            if ($c['kind'] === 'quantity' && $p->target > 0 && ($e->unit === null || $e->unit === $p->target_unit)) {
                $cand = ['pct' => round($e->numeric_value / $p->target * 100, 2), 'label' => $c['label'], 'evidence' => $e, 'stages' => []];
                if (! $best || $e->observed_at->gt($best['evidence']->observed_at)) $best = $cand;
            }
        }
        $stages = $this->currentOperational()->filter(fn ($e) => ($catalog[$e->metric_key]['kind'] ?? null) === 'stage' && $e->numeric_value !== null)
            ->sortBy(fn ($e) => $catalog[$e->metric_key]['stage']);
        if ($stages->isNotEmpty()) {
            $target = $this->latest('target_endpoints')?->numeric_value ?: ($p->target_unit === 'endpoints' ? $p->target : null);
            if ($target) {
                $list = $stages->map(fn ($e) => ['metric' => $e->metric_key, 'label' => $catalog[$e->metric_key]['label'], 'value' => $e->numeric_value,
                    'pct' => round($e->numeric_value / $target * 100, 2), 'evidence' => $e])->values()->all();
                $final = end($list);
                $cand = ['pct' => $final['pct'], 'label' => $final['label'], 'evidence' => $final['evidence'], 'stages' => $list];
                if (! $best || $final['evidence']->observed_at->gte($best['evidence']->observed_at)) $best = $cand;
            }
        }
        return $best;
    }

    /** Progress used for pressure maths: operational when available else reported. */
    public function effectiveProgress(): array
    {
        $o = $this->observedProgress();
        if ($o) return ['pct' => $o['pct'], 'basis' => 'OPERATIONAL', 'label' => $o['label']];
        if ($this->project->reported_progress_pct !== null) return ['pct' => $this->project->reported_progress_pct, 'basis' => 'REPORTED', 'label' => 'Progress dilaporkan'];
        return ['pct' => null, 'basis' => 'UNKNOWN', 'label' => 'Tidak ada data progress'];
    }

    public function newestEvidenceAt(): ?CarbonImmutable
    {
        $d = $this->evidence->where('is_current', true)->max('observed_at');
        return $d ? CarbonImmutable::parse($d) : null;
    }
}
