@extends('layouts.app')
@section('crumb', 'Attention Analysis / ' . $project->project_code)
@section('content')
<x-page-title :title="$project->name . ' — Attention'" :sub="$project->project_code . ' · ' . $project->customer">
    <a href="{{ route('projects.show', $project) }}" class="btn-secondary">Detail proyek</a>
</x-page-title>
@if (! $assessment)<div class="card"><x-empty title="Belum ada penilaian attention" /></div>@else
<div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card card-pad">
        <div class="label-sm">Level attention</div><div class="mt-1 flex items-center gap-3"><x-badge :value="$assessment->level" class="!text-[13px] !px-3 !py-1" /><span class="text-[28px] font-bold tabular-nums">{{ number_format($assessment->score, 1) }}</span><span class="text-ink-500">/ 100</span></div>
        <div class="text-[12px] text-ink-500 mt-1">Keyakinan data {{ round($assessment->confidence * 100) }}% · dihitung {{ $assessment->created_at->format('d M Y H:i') }} · as-of {{ $assessment->as_of->format('d M Y H:i') }}</div>
        <p class="text-[13px] mt-3 text-ink-700">{{ $assessment->explanation }}</p>
        <p class="text-[11px] text-ink-500 mt-3">Skor adalah prioritas perhatian manajemen, bukan probabilitas kegagalan. Nilai hilang tidak dihitung sebagai nol risiko.</p>
    </div>
    <div class="card card-pad lg:col-span-2"><h2 class="mb-3">Kontribusi kriteria</h2><x-score-breakdown :assessment="$assessment" /></div>
</div>
<div class="card card-pad mb-4">
    <h2 class="mb-3">Temuan terkait &amp; evidence pendukung</h2>
    @forelse ($findings as $f)
        <div class="border border-line rounded-lg p-3 mb-3" id="finding-{{ $f->id }}">
            <div class="flex flex-wrap items-center gap-2"><x-badge :value="$f->severity" kind="severity" /><span class="badge bg-white text-ink-700 border-line">{{ str_replace('_', ' ', $f->category) }}</span><x-badge :value="$f->verification_status" kind="verification" /></div>
            <div class="font-medium mt-1.5">{{ $f->title }}</div><p class="text-[13px] text-ink-700">{{ $f->description }}</p>
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-2 mt-2">@foreach ($f->evidence as $e)<x-evidence-card :e="$e" :role="$e->pivot->role" />@endforeach</div>
        </div>
    @empty<x-empty title="Tidak ada temuan aktif" />@endforelse
</div>
<div class="card card-pad"><h2 class="mb-2">Riwayat penilaian</h2>
    <table class="tbl"><thead><tr><th>Dihitung</th><th>As-of</th><th>Level</th><th>Skor</th><th>Keyakinan</th></tr></thead><tbody>
    @foreach ($history as $h)<tr><td>{{ $h->created_at->format('d M Y H:i') }}</td><td>{{ $h->as_of->format('d M Y H:i') }}</td><td><x-badge :value="$h->level" /></td><td class="tabular-nums">{{ number_format($h->score, 1) }}</td><td>{{ round($h->confidence * 100) }}%</td></tr>@endforeach</tbody></table></div>
@endif
@endsection
