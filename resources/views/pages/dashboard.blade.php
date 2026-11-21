@extends('layouts.app')
@section('crumb', 'Overview')
@section('content')
<x-page-title title="Portfolio Overview" :sub="'Tanggal acuan data ' . $asOf->format('d M Y, H:i') . ' · seluruh angka dihitung langsung dari data proyek yang tersimpan'">
    <a href="{{ route('data.index') }}" class="btn-secondary">Data Management</a>
</x-page-title>

@if (($pendingEvidence ?? 0) > 0 && \App\Support\CurrentUser::get()?->isDirector())
    <x-alert type="warning"><b>{{ $pendingEvidence }} bukti operasional menunggu tinjauan Anda.</b> <a href="{{ route('evidence.index') }}" class="underline font-medium">Tinjau sekarang →</a></x-alert>
@endif
@if ($kpi['total'] === 0)
    <div class="card"><x-empty title="Belum ada data proyek" text="Impor Project Master, Project Updates, dan Production Data untuk mulai." :href="route('data.index')" action="Buka Data Management" /></div>
@else
<div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3 mb-5">
    <x-kpi label="Total proyek" :value="$kpi['total']" />
    <x-kpi label="High attention" :value="$kpi['high']" tone="crit" :href="route('projects.index', ['level' => 'HIGH'])" />
    <x-kpi label="Medium attention" :value="$kpi['medium']" tone="warn" :href="route('projects.index', ['level' => 'MEDIUM'])" />
    <x-kpi label="Low attention" :value="$kpi['low']" tone="good" :href="route('projects.index', ['level' => 'LOW'])" />
    <x-kpi label="Perlu verifikasi" :value="$kpi['verification']" tone="unv" hint="issue belum terverifikasi" />
    <x-kpi label="Follow-up terbuka" :value="$kpi['open_followups']" tone="pend" :href="route('follow-ups.index')" />
    <x-kpi label="Follow-up overdue" :value="$kpi['overdue']" tone="crit" />
</div>

<div class="grid lg:grid-cols-3 gap-3 mb-5">
    <div class="card card-pad"><h2 class="mb-2">Distribusi attention</h2><div class="h-56"><canvas id="chAttention"></canvas></div></div>
    <div class="card card-pad"><h2 class="mb-2">Progress: dilaporkan vs observed</h2><div class="h-56"><canvas id="chProgress"></canvas></div></div>
    <div class="card card-pad"><h2 class="mb-2">Status follow-up</h2><div class="h-56"><canvas id="chFollow"></canvas></div></div>
</div>

<livewire:portfolio-table />
@endif
@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.umd.js') }}"></script>
<script>
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif'; Chart.defaults.font.size = 11; Chart.defaults.color = '#64748B';
    const kpi = @json($kpi), prog = @json($progress), fu = @json($fuStatus);
    new Chart(document.getElementById('chAttention'), { type: 'doughnut',
        data: { labels: ['High', 'Medium', 'Low'], datasets: [{ data: [kpi.high, kpi.medium, kpi.low], backgroundColor: ['#EF4444', '#F59E0B', '#10B981'], borderWidth: 0 }] },
        options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } } });
    new Chart(document.getElementById('chProgress'), { type: 'bar',
        data: { labels: prog.map(p => p.code), datasets: [
            { label: 'Dilaporkan', data: prog.map(p => p.reported), backgroundColor: '#CBD5E1' },
            { label: 'Observed (operasional)', data: prog.map(p => p.observed), backgroundColor: '#2563EB' }] },
        options: { maintainAspectRatio: false, scales: { y: { min: 0, max: 100, grid: { color: '#F1F5F9' } }, x: { grid: { display: false } } }, plugins: { legend: { position: 'bottom' } } } });
    new Chart(document.getElementById('chFollow'), { type: 'bar',
        data: { labels: Object.keys(fu).map(s => s.replace('_', ' ')), datasets: [{ data: Object.values(fu), backgroundColor: ['#6366F1', '#F59E0B', '#10B981', '#94A3B8'] }] },
        options: { maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } }, y: { grid: { display: false } } }, plugins: { legend: { display: false } } } });
</script>
@endpush
