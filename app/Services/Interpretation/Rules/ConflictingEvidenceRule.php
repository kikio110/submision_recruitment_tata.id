<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class ConflictingEvidenceRule extends Rule {
    public function key(): string { return 'conflicting_evidence'; }
    public function evaluate(ProjectContext $c): array {
        $out = [];
        foreach ($c->evidence->whereNotNull('conflict_group')->groupBy('conflict_group') as $g => $list) {
            $f = $list->first();
            $vals = $list->map(fn ($e) => "{$e->observed_value} [{$e->source_ref}, {$this->fmt($e->observed_at)}]")->implode(' vs ');
            $out[] = $this->draft("conflict:$g", 'CONFLICTING_EVIDENCE', 'MEDIUM', "Evidence bertentangan: {$f->metric_key}" . ($f->subject_key ? " ({$f->subject_key})" : ''),
                "Sumber berbeda mencatat nilai berbeda untuk metrik yang sama pada waktu berdekatan: $vals. Kedua nilai dipertahankan dan tidak ada yang ditimpa; perlu verifikasi.",
                $list->all(), [$f->metric_key]);
        }
        return $out;
    }
}
