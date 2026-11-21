<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class MissingPrerequisiteRule extends Rule {
    public function key(): string { return 'missing_prerequisite'; }
    public function evaluate(ProjectContext $c): array {
        $cur = $c->latest('connection_status', null, 'OPERATIONAL');
        if (! $cur || in_array($cur->observed_value, config('ontrack.healthy.connection_status'), true)) return [];
        $waiting = $c->messages()->filter(fn ($m) => in_array('WAITING', $m->signalList()))->sortBy('observed_at')->values();
        $ev = array_merge([$cur], $waiting->take(-3)->map(fn ($m) => [$m, 'SUPPORTS'])->all());
        $w = $waiting->isNotEmpty() ? ' Update terkait melaporkan proyek menunggu prasyarat dari customer (' . $waiting->pluck('source_ref')->implode(', ') . ').' : '';
        return [$this->draft('connection_status', 'MISSING_PREREQUISITE', 'HIGH', "Prasyarat operasional belum tersedia: koneksi {$cur->observed_value}",
            "Environment production tercatat {$cur->observed_value} pada {$this->fmt($cur->observed_at)}; belum ada koneksi berhasil yang tercatat, sehingga pengujian production belum dapat dilakukan.$w",
            $ev, ['connection_status'], $cur->observed_at)];
    }
}
