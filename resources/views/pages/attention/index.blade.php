@extends('layouts.app')
@section('crumb', 'Attention Analysis')
@section('content')
<x-page-title title="Attention Analysis" sub="Prioritas perhatian manajemen — bukan probabilitas proyek gagal. Kriteria tanpa data ditandai UNKNOWN, bukan aman." />
@if ($items->isEmpty())<div class="card"><x-empty title="Belum ada penilaian" text="Impor data untuk menghasilkan analisis." :href="route('data.index')" action="Data Management" /></div>@endif
<div class="grid xl:grid-cols-2 gap-4">
@foreach ($items as $a)
    <div class="card card-pad">
        <div class="flex items-start justify-between gap-2 mb-3">
            <div><a href="{{ route('projects.show', $a->project) }}" class="font-semibold text-accent-600">{{ $a->project->project_code }} · {{ $a->project->name }}</a>
                <div class="text-[12px] text-ink-500">Keyakinan data {{ round($a->confidence * 100) }}% · as-of {{ $a->as_of->format('d M Y H:i') }}</div></div>
            <div class="text-right"><x-badge :value="$a->level" /><div class="text-[22px] font-bold tabular-nums">{{ number_format($a->score, 1) }}</div></div>
        </div>
        <x-score-breakdown :assessment="$a" />
        <a href="{{ route('projects.attention', $a->project) }}" class="btn-secondary mt-4 !h-8">Penjelasan &amp; evidence</a>
    </div>
@endforeach
</div>
@endsection
