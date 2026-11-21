<?php
namespace App\Services\Interpretation;

use App\Models\EvidenceRecord;
use App\Services\ProjectContext;
use Carbon\CarbonInterface;

/** A reusable interpretation rule: depends on evidence types/categories, never on a project id. */
abstract class Rule
{
    abstract public function key(): string;

    /** @return array<int,array> finding drafts */
    abstract public function evaluate(ProjectContext $ctx): array;

    protected function draft(string $subject, string $category, string $severity, string $title, string $description,
        array $evidence, array $metrics = [], ?CarbonInterface $baseline = null): array
    {
        $ev = [];
        foreach ($evidence as $e) {
            [$rec, $role] = is_array($e) ? $e : [$e, 'SUPPORTS'];
            if ($rec instanceof EvidenceRecord) $ev[$rec->id . $role] = [$rec, $role];
        }
        $ev = array_values($ev);
        $base = $baseline ?? collect($ev)->map(fn ($x) => $x[0]->observed_at)->max();
        return ['rule_key' => $this->key(), 'subject_key' => $subject, 'category' => $category, 'severity' => $severity,
            'title' => $title, 'description' => $description, 'evidence' => $ev, 'metrics' => $metrics, 'baseline_at' => $base];
    }

    protected function fmt(CarbonInterface $d): string { return $d->format('d M Y H:i'); }
    protected function th(string $k) { return config("ontrack.thresholds.$k"); }
    protected function lower(string $s): string
    {
        $o = ['INFO', 'LOW', 'MEDIUM', 'HIGH', 'CRITICAL']; $i = array_search($s, $o);
        return $o[max(1, $i - 1)];
    }
}
