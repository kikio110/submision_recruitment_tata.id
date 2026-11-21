<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class PendingQuantityRule extends Rule {
    public function key(): string { return 'pending_quantity'; }
    public function evaluate(ProjectContext $c): array {
        $pend = $c->latest('pending_internal_check_quantity', null, 'OPERATIONAL'); $ver = $c->latest('verified_quantity', null, 'OPERATIONAL');
        if (! $pend || ! $ver || $pend->numeric_value <= 0) return [];
        $total = $pend->numeric_value + $ver->numeric_value; $share = round($pend->numeric_value / $total * 100, 1);
        if ($share < $this->th('pending_share_pct')) return [];
        $sev = $share >= 25 ? 'HIGH' : ($share >= 10 ? 'MEDIUM' : 'LOW');
        $act = $c->latest('reported_actual', null, 'REPORTED');
        $rel = $act && abs($act->numeric_value - $total) < 0.5 ? " Angka aktual yang dilaporkan ({$act->numeric_value} {$ver->unit}) = terverifikasi + menunggu cek internal." : '';
        return [$this->draft('pending', 'PROGRESS_DISCREPANCY', $sev, 'Sebagian hasil masih menunggu verifikasi internal',
            "Terverifikasi {$ver->numeric_value} {$ver->unit}, menunggu cek internal {$pend->numeric_value} {$pend->unit} ($share% dari total {$total}).$rel Progress yang aman dipakai adalah angka terverifikasi.",
            array_filter([$ver, $pend, $act ? [$act, 'CONTRAST'] : null]), ['verified_quantity', 'pending_internal_check_quantity'])];
    }
}
