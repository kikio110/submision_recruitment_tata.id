@extends('layouts.app')
@section('crumb', 'Projects / ' . $project->project_code)
@section('content')
@php $pct = fn ($v) => $v === null ? 'n/a' : rtrim(rtrim(number_format($v, 1), '0'), '.') . '%'; @endphp
<x-page-title :title="$project->name" :sub="$project->project_code . ' · ' . $project->customer . ' · ' . ($project->project_type ?? '—')">
    <a href="{{ route('projects.attention', $project) }}" class="btn-secondary">Attention analysis</a>
    @if (app(\App\Services\ProjectReportService::class)->canReport(\App\Support\CurrentUser::get(), $project))<a href="{{ route('projects.report.form', $project) }}" class="btn-secondary">Lapor update</a><a href="{{ route('evidence.create', $project) }}" class="btn-secondary">Kirim bukti operasional</a>@endif
    @if (\App\Support\CurrentUser::get()?->isDirector())<a href="{{ route('projects.edit', $project) }}" class="btn-secondary">Ubah proyek</a><a href="{{ route('follow-ups.create', ['project' => $project->id]) }}" class="btn-accent">Buat follow-up</a>@endif
</x-page-title>

{{-- 1. Overview --}}
<div class="grid lg:grid-cols-3 gap-3 mb-4">
    <div class="card card-pad lg:col-span-2">
        <div class="flex flex-wrap items-center gap-2 mb-2"><h2>Project overview</h2>
            @if ($assessment)<x-badge :value="$assessment->level" /> <span class="text-[12px] text-ink-500">skor {{ number_format($assessment->score, 1) }} · keyakinan {{ round($assessment->confidence * 100) }}%</span>@endif</div>
        <p class="text-[13px] text-ink-700">{{ $assessment?->explanation ?? 'Belum ada penilaian attention.' }}</p>
        <a class="text-[12px] text-accent-600 font-medium mt-2 inline-block" href="{{ route('projects.attention', $project) }}">Lihat penjelasan kriteria dan evidence →</a>
    </div>
    <div class="card card-pad">
        <h2 class="mb-2">Progress &amp; deadline</h2>
        <div class="grid grid-cols-2 gap-3">
            <div><div class="label-sm">👤 Dilaporkan</div><div class="text-[22px] font-bold">{{ $pct($project->reported_progress_pct) }}</div></div>
            <div><div class="label-sm">🛡 Observed</div><div class="text-[22px] font-bold">{{ $observed ? $pct($observed['pct']) : 'n/a' }}</div></div>
        </div>
        @if ($observed)<p class="text-[11px] text-ink-500 mt-1">Basis observed: {{ $observed['label'] }} ({{ $observed['evidence']->source_ref }})</p>@else<p class="text-[11px] text-ink-500 mt-1">Belum ada basis operasional untuk menghitung progress observed (bukan 0%).</p>@endif
        <div class="mt-3 text-[13px] space-y-0.5">
            <div>Waktu terpakai: <b>{{ $ctx->elapsedPct() !== null ? $ctx->elapsedPct() . '%' : 'n/a' }}</b></div>
            <div>Deadline: <b>{{ $project->deadline?->format('d M Y') }}</b> @if (($d = $ctx->daysLeft()) !== null)<span class="text-ink-500">({{ $d >= 0 ? "sisa $d hari" : 'terlewat ' . abs($d) . ' hari' }})</span>@endif</div>
        </div>
    </div>
</div>

{{-- 2. Project information --}}
<div class="card card-pad mb-4">
    <h2 class="mb-2">Informasi proyek <span class="badge bg-well text-ink-700 border-dashed border-slate-400 ml-1">👤 Project master (dilaporkan)</span></h2>
    <dl class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-[13px]">
        @foreach ([['Customer', $project->customer], ['Tipe', $project->project_type], ['PIC', $project->pic], ['Status dilaporkan', $project->reported_status], ['Mulai', $project->start_date?->format('d M Y')], ['Deadline', $project->deadline?->format('d M Y')],
            ['Target', $project->target . ' ' . $project->target_unit], ['Aktual dilaporkan', $project->reported_actual !== null ? $project->reported_actual . ' ' . $project->target_unit : 'n/a'], ['Update master terakhir', $project->last_reported_update_at?->format('d M Y H:i')], ['Revisi master', $project->revision]] as [$l, $v])
            <div><dt class="label-sm">{{ $l }}</dt><dd class="font-medium">{{ $v ?: '—' }}</dd></div>@endforeach
    </dl>
</div>

<div class="grid xl:grid-cols-5 gap-4 mb-4">
    {{-- 4. Timeline --}}
    <div class="card card-pad xl:col-span-3">
        <h2 class="mb-1">Timeline kronologis</h2><p class="text-[12px] text-ink-500 mb-3">Update yang dilaporkan dan observasi sistem operasional, diurutkan berdasarkan timestamp.</p>
        @if ($timeline->isEmpty())<x-empty title="Belum ada update atau evidence" text="Proyek ini belum memiliki data selain master." />
        @else
        <ol class="relative border-l border-line ml-2 space-y-3">
            @foreach ($timeline as $e)
                @php $op = $e->evidence_class === 'OPERATIONAL'; @endphp
                <li class="pl-4 relative"><span class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full {{ $op ? 'bg-good-fg' : 'bg-slate-400' }}"></span>
                    <div class="text-[11px] text-ink-500">{{ $e->observed_at->format('d M Y H:i') }} · <span class="font-mono">{{ $e->source_ref }}</span> · {{ $e->sourceText() }}
                        <span class="badge {{ $op ? 'bg-good-bg text-good-fg border-good-bd' : 'bg-well text-ink-700 border-dashed border-slate-400' }} ml-1">{{ $op ? '🛡 Operasional' : '👤 Dilaporkan' }}</span>
                        @if ($e->conflict_group)<span class="badge bg-crit-bg text-crit-fg border-crit-bd">Conflicting</span>@endif
                        @if (! $e->is_current && $op)<span class="badge bg-well text-ink-500 border-line">Riwayat</span>@endif</div>
                    <div class="text-[13px]">{{ $e->summary }}</div></li>
            @endforeach
        </ol>@endif
    </div>

    {{-- 5. Operational evidence --}}
    <div class="card card-pad xl:col-span-2">
        <h2 class="mb-1">Operational evidence</h2><p class="text-[12px] text-ink-500 mb-3">Observasi sistem lain. Nilai lama tidak dihapus; kondisi terkini ditentukan timestamp.</p>
        @php $ops = $ctx->operational()->sortByDesc('observed_at')->values(); @endphp
        @if ($ops->isEmpty())<x-empty title="Belum ada evidence operasional" text="Kondisi sebenarnya belum diketahui — ini bukan berarti aman." />
        @else<div class="space-y-2 max-h-[640px] overflow-y-auto pr-1">@foreach ($ops as $e)<x-evidence-card :e="$e" />@endforeach</div>@endif
    </div>
</div>

@php $subs = $project->evidenceSubmissions()->with(['submitter', 'reviewer'])->limit(5)->get(); @endphp
@if ($subs->isNotEmpty())
<div class="card card-pad mb-4"><div class="flex items-center justify-between mb-2"><h2>Bukti yang diajukan</h2><a href="{{ route('evidence.index', ['status' => 'ALL']) }}" class="text-[12px] text-accent-600 font-medium">Semua pengajuan →</a></div>
    <p class="text-[12px] text-ink-500 mb-2">Hanya bukti yang disetujui Director yang dihitung sebagai evidence operasional dan tampil di panel evidence di atas.</p>
    @foreach ($subs as $sb)<div class="flex flex-wrap items-center gap-2 py-1.5 border-b border-well last:border-0 text-[13px]"><span class="badge {{ ['PENDING' => 'bg-pend-bg text-pend-fg border-pend-bd', 'APPROVED' => 'bg-good-bg text-good-fg border-good-bd', 'REJECTED' => 'bg-crit-bg text-crit-fg border-crit-bd'][$sb->status] }}">{{ ['PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'][$sb->status] }}</span><span>{{ $sb->metricLabel() }}@if ($sb->subject_key) ({{ $sb->subject_key }})@endif = <b>{{ $sb->observed_value }}</b></span><span class="text-ink-500 text-[12px]">{{ $sb->observed_at->format('d M Y H:i') }} · {{ $sb->submitter?->name }}</span></div>@endforeach</div>
@endif

{{-- 6/7. Findings and attention explanation --}}
<div class="card card-pad mb-4">
    <h2 class="mb-3">Temuan terdeteksi</h2>
    @forelse ($findings as $f)
        <div class="border border-line rounded-lg p-3 mb-2 {{ $f->is_active ? '' : 'opacity-60' }}" id="finding-{{ $f->id }}">
            <div class="flex flex-wrap items-center gap-2"><x-badge :value="$f->severity" kind="severity" /><span class="badge bg-white text-ink-700 border-line">{{ str_replace('_', ' ', $f->category) }}</span>
                <x-badge :value="$f->verification_status" kind="verification" />@unless ($f->is_active)<span class="badge bg-well text-ink-500 border-line">Tidak terdeteksi lagi</span>@endunless
                <span class="ml-auto text-[11px] text-ink-500">terdeteksi {{ $f->detected_at->format('d M Y H:i') }}</span></div>
            <div class="font-medium mt-1.5">{{ $f->title }}</div><p class="text-[13px] text-ink-700 mt-0.5">{{ $f->description }}</p>
            <details class="mt-2"><summary class="text-[12px] text-accent-600 font-medium cursor-pointer">Evidence pendukung ({{ $f->evidence->count() }})</summary>
                <div class="grid md:grid-cols-2 gap-2 mt-2">@foreach ($f->evidence as $e)<x-evidence-card :e="$e" :role="$e->pivot->role" />@endforeach</div></details>
            @if (\App\Support\CurrentUser::get()?->isDirector() && $f->is_active && $f->severity !== 'INFO')<a href="{{ route('follow-ups.create', ['project' => $project->id, 'finding' => $f->id]) }}" class="btn-secondary mt-2 !h-8">Buat follow-up</a>@endif
        </div>
    @empty<x-empty title="Tidak ada temuan" text="Tidak ada kondisi bermasalah yang terdeteksi dari data yang ada." />@endforelse
</div>

<div class="grid lg:grid-cols-2 gap-4 mb-4">
    <div class="card card-pad"><h2 class="mb-3">Penjelasan attention</h2>@if ($assessment)<x-score-breakdown :assessment="$assessment" />@else<x-empty title="Belum dinilai" />@endif</div>
    <div class="card card-pad">
        <h2 class="mb-3">Follow-up terkait</h2>
        @forelse ($project->followUps as $fu)
            <a href="{{ route('follow-ups.show', $fu) }}" class="block border border-line rounded-lg p-3 mb-2 hover:border-slate-300">
                <div class="flex items-center gap-2 flex-wrap"><x-badge :value="$fu->status" kind="followup" /> @if ($fu->finding)<x-badge :value="$fu->finding->verification_status" kind="verification" />@endif<span class="ml-auto text-[12px] text-ink-500">{{ $fu->assigned_pic }} · {{ $fu->due_date?->format('d M Y') ?? 'tanpa due date' }}</span></div>
                <div class="font-medium mt-1">{{ $fu->title }}</div></a>
        @empty<x-empty title="Belum ada follow-up" text="Buat follow-up dari salah satu temuan di atas." />@endforelse
    </div>
</div>
@endsection
