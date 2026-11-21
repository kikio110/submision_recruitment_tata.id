<?php
namespace App\Services\Interpretation\Rules;
use App\Services\Interpretation\Rule;
use App\Services\ProjectContext;
class UsageHealthRule extends Rule {
    public function key(): string { return 'usage_health'; }
    public function evaluate(ProjectContext $c): array {
        $ok = $c->latest('successful_logins_last_24h', null, 'OPERATIONAL'); $bad = $c->latest('failed_logins_last_24h', null, 'OPERATIONAL');
        $users = $c->latest('active_users_last_24h', null, 'OPERATIONAL'); $out = [];
        if ($ok && $bad && $bad->numeric_value > 0) {
            $tot = $ok->numeric_value + $bad->numeric_value; $ratio = $bad->numeric_value / max(1, $tot);
            $sys = $ratio > $this->th('failed_login_ratio');
            $out[] = $this->draft('login', 'OPERATIONAL_BLOCKAGE', $sys ? 'MEDIUM' : 'INFO', $sys ? 'Kegagalan login tinggi' : 'Login gagal terisolasi (bukan indikasi gangguan sistemik)',
                "{$bad->numeric_value} dari " . (int) $tot . ' percobaan login gagal (' . round($ratio * 100, 1) . '%). ' . ($sys ? 'Rasio melewati ambang sehingga perlu diperiksa.' : 'Di bawah ambang, aplikasi ONLINE dan pengguna aktif; satu kegagalan tidak disimpulkan sebagai gangguan sistemik.'),
                array_filter([$bad, $ok, $users]), ['failed_logins_last_24h', 'successful_logins_last_24h']);
        }
        $dep = $c->latest('application_status', null, 'OPERATIONAL');
        if ($users && $dep && $users->numeric_value == 0 && in_array($dep->observed_value, config('ontrack.healthy.application_status'), true))
            $out[] = $this->draft('adoption', 'OPERATIONAL_BLOCKAGE', 'MEDIUM', 'Aplikasi aktif tetapi belum ada pengguna aktif',
                'Evidence deployment menunjukkan aplikasi online, tetapi evidence penggunaan mencatat 0 pengguna aktif; deployment tidak sama dengan pemakaian.', [$dep, $users], ['active_users_last_24h']);
        return $out;
    }
}
