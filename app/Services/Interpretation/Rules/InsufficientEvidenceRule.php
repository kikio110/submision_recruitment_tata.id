<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class InsufficientEvidenceRule extends Rule {
    public function key(): string { return 'insufficient_evidence'; }
    public function evaluate(ProjectContext $c): array {
        if ($c->operational()->isNotEmpty()) return [];
        return [$this->draft('no_operational', 'INSUFFICIENT_EVIDENCE', 'MEDIUM', 'Tidak ada evidence operasional untuk memvalidasi laporan',
            'Proyek hanya memiliki informasi yang dilaporkan. Kondisi sebenarnya tidak diketahui; ketiadaan data tidak diartikan sebagai aman.', array_filter([$c->latest('reported_status'), $c->latest('reported_progress_pct')]), ['*'], $c->project->last_reported_update_at ?? $c->asOf)];
    }
}
