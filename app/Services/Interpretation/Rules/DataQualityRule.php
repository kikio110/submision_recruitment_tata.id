<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class DataQualityRule extends Rule {
    public function key(): string { return 'data_quality'; }
    public function evaluate(ProjectContext $c): array {
        $out = []; $p = $c->project;
        $pct = $c->latest('reported_progress_pct', null, 'REPORTED'); $act = $c->latest('reported_actual', null, 'REPORTED');
        if ($pct && $act && $p->target > 0) {
            $calc = round($act->numeric_value / $p->target * 100, 2); $d = abs($pct->numeric_value - $calc);
            if ($d > $this->th('internal_pct_tolerance'))
                $out[] = $this->draft('master_internal', 'DATA_QUALITY', $d >= 5 ? 'MEDIUM' : 'LOW', 'Data project master tidak konsisten secara internal',
                    "Progress dilaporkan {$pct->numeric_value}% tetapi aktual {$act->numeric_value} dari target {$p->target} {$p->target_unit} setara $calc%. Selisih " . round($d, 2) . ' poin pada record yang sama.', [$pct, $act], ['reported_progress_pct', 'reported_actual']);
        } elseif (! $pct && ! $act) {
            $out[] = $this->draft('master_missing', 'DATA_QUALITY', 'LOW', 'Progress tidak dilaporkan pada project master', 'Field progress dan aktual kosong; kondisi tidak diasumsikan nol.', array_filter([$c->latest('reported_status')]), []);
        }
        foreach ($c->currentOperational()->filter(fn ($e) => (config("ontrack.metrics.{$e->metric_key}.kind") === 'quality') && $e->numeric_value > 0) as $q) {
            $share = $p->target > 0 ? round($q->numeric_value / $p->target * 100, 2) : null;
            $sev = $share === null ? 'LOW' : ($share >= 10 ? 'HIGH' : ($share >= 2 ? 'MEDIUM' : 'LOW'));
            $msgs = $c->messages()->filter(fn ($m) => array_intersect(['HOLD', 'QUALITY'], $m->signalList()))->take(-2)->map(fn ($m) => [$m, 'SUPPORTS'])->all();
            $out[] = $this->draft("quality:{$q->metric_key}", 'DATA_QUALITY', $sev, "Masalah kualitas data sumber: {$q->summary}",
                "Tercatat {$q->numeric_value} {$q->unit} dilewati/tidak lengkap" . ($share !== null ? " ($share% dari target)" : '') . " pada {$this->fmt($q->observed_at)}. Ini masalah kualitas data, terpisah dari selisih progress, dan menurunkan keyakinan atas jumlah terverifikasi.",
                array_merge([$q], $msgs), [$q->metric_key]);
        }
        return $out;
    }
}
