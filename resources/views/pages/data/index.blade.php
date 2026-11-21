@extends('layouts.app')
@section('crumb', 'Data Management')
@section('content')
<x-page-title title="Data Management" sub="Impor CSV/JSON, validasi, dan segarkan interpretasi tanpa kehilangan follow-up.">
    <a href="{{ route('data.imports') }}" class="btn-secondary">Riwayat import</a>
    @if (\App\Support\CurrentUser::get()?->isDirector())<a href="{{ route('projects.create') }}" class="btn-secondary">+ Tambah proyek manual</a>@endif
    @if (\App\Support\CurrentUser::get()?->isDirector())
        <button type="button" class="btn-secondary" x-data @click="$dispatch('open-modal', 'reprocess')">↻ Hitung ulang</button>
        <x-modal-confirm id="reprocess" title="Hitung ulang semua proyek" :action="route('data.reprocess')" confirm="Hitung ulang">Interpretasi, attention, dan verifikasi dihitung ulang dari data tersimpan. Follow-up tidak berubah.</x-modal-confirm>
    @endif
</x-page-title>
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
    <x-kpi label="Proyek" :value="$counts['projects']" /><x-kpi label="Update aktif" :value="$counts['updates']" /><x-kpi label="Record produksi" :value="$counts['production']" /><x-kpi label="Evidence" :value="$counts['evidence']" /><x-kpi label="As-of" :value="$asOf->format('d M')" :hint="$asOf->format('Y H:i')" tone="pend" />
</div>
<div class="grid xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2"><livewire:data-upload /></div>
    <div class="card card-pad"><h2 class="mb-3">Import terakhir</h2>
        @forelse ($recent as $b)<a href="{{ route('data.imports.show', $b) }}" class="block py-2 border-b border-well last:border-0"><div class="flex items-center justify-between gap-2"><span class="font-medium text-[13px] truncate">{{ $b->file_name }}</span><x-badge :value="$b->status" kind="import" /></div><div class="text-[11px] text-ink-500">{{ $b->created_at->format('d M Y H:i') }} · {{ $b->message }}</div></a>
        @empty<x-empty title="Belum ada import" />@endforelse</div>
</div>
@endsection
