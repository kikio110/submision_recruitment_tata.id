<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class OperationalStatusRule extends Rule {
    public function key(): string { return 'operational_status'; }
    public function evaluate(ProjectContext $c): array {
        $out = []; $healthy = config('ontrack.healthy');
        foreach (['scanner_status', 'application_status', 'deployment_status'] as $metric) {
            $subjects = $c->operational()->where('metric_key', $metric)->pluck('subject_key')->unique();
            foreach ($subjects as $s) {
                $cur = $c->latest($metric, $s, 'OPERATIONAL');
                if (! $cur || in_array($cur->observed_value, $healthy[$metric], true)) continue;
                $v = strtoupper($cur->observed_value);
                $sev = preg_match('/OUT_OF_SERVICE|DOWN|OFFLINE|FAIL|ERROR|STOP/', $v) ? 'HIGH' : 'MEDIUM';
                $hist = $c->history($metric, $s);
                $trail = $hist->map(fn ($e) => "{$e->observed_value} ({$this->fmt($e->observed_at)})")->implode(' → ');
                $label = $s ? "$metric $s" : $metric;
                $ev = $hist->map(fn ($e) => [$e, $e->id === $cur->id ? 'SUPPORTS' : 'CONTRAST'])->all();
                $out[] = $this->draft("$metric:$s", 'OPERATIONAL_BLOCKAGE', $sev, "Kondisi operasional tidak sehat: $label = $v",
                    "Status terakhir $label adalah $v pada {$this->fmt($cur->observed_at)}. Riwayat dipertahankan: $trail.", $ev, [$metric], $cur->observed_at);
            }
        }
        return $out;
    }
}
