@extends('layouts.app')
@section('crumb', 'Tinjauan bukti')
@section('content')
@php $isDir = \App\Support\CurrentUser::get()?->isDirector(); @endphp
<x-page-title title="Tinjauan bukti operasional" :sub="$isDir ? 'Bukti yang diajukan PIC menunggu persetujuan Anda. Hanya yang disetujui dihitung sebagai evidence.' : 'Status pengajuan bukti untuk proyek Anda.'" />
<div class="flex gap-2 mb-3">
    @foreach (['PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'] as $k => $l)
        <a href="{{ route('evidence.index', ['status' => $k]) }}" class="btn {{ $status === $k ? 'bg-navy-900 text-white border-navy-900' : 'btn-secondary' }}">{{ $l }} <span class="opacity-70 tabular-nums">{{ $counts[$k] ?? 0 }}</span></a>@endforeach
    <a href="{{ route('evidence.index', ['status' => 'ALL']) }}" class="btn-ghost">Semua</a>
</div>
<div class="space-y-3">
@forelse ($items as $s)
    <div class="card card-pad" id="submission-{{ $s->id }}">
        <div class="flex flex-wrap items-center gap-2">
            <span class="badge {{ ['PENDING' => 'bg-pend-bg text-pend-fg border-pend-bd', 'APPROVED' => 'bg-good-bg text-good-fg border-good-bd', 'REJECTED' => 'bg-crit-bg text-crit-fg border-crit-bd'][$s->status] }}">{{ ['PENDING' => 'Menunggu tinjauan', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'][$s->status] }}</span>
            <a href="{{ route('projects.show', $s->project) }}" class="font-medium text-accent-600">{{ $s->project->project_code }} · {{ $s->project->name }}</a>
            @if ($s->followUp)<a href="{{ route('follow-ups.show', $s->followUp) }}" class="text-[12px] text-accent-600">follow-up #{{ $s->follow_up_id }}</a>@endif
            <span class="ml-auto text-[11px] text-ink-500">diajukan {{ $s->created_at->format('d M Y H:i') }} oleh {{ $s->submitter?->name ?? '—' }}</span></div>
        <div class="mt-2 text-[14px] font-medium">{{ $s->metricLabel() }}@if ($s->subject_key) <span class="text-ink-500">({{ $s->subject_key }})</span>@endif = {{ $s->observed_value }} {{ $s->unit }}</div>
        <dl class="grid md:grid-cols-3 gap-3 mt-2 text-[12px]"><div><dt class="label-sm">Waktu observasi</dt><dd>{{ $s->observed_at->format('d M Y H:i') }}</dd></div><div><dt class="label-sm">Sistem asal</dt><dd>{{ $s->source_system }}</dd></div><div><dt class="label-sm">Asal bukti</dt><dd class="whitespace-pre-line">{{ $s->source_note }}</dd></div></dl>
        @if ($s->status !== 'PENDING')<div class="mt-2 text-[12px] bg-canvas rounded p-2">Ditinjau {{ $s->reviewed_at?->format('d M Y H:i') }} oleh {{ $s->reviewer?->name }}@if ($s->review_note) — {{ $s->review_note }}@endif</div>@endif
        @if ($isDir && $s->status === 'PENDING')
            <div class="mt-3 flex gap-2"><button type="button" class="btn-accent" x-data @click="$dispatch('open-modal', 'approve-{{ $s->id }}')">Setujui</button><button type="button" class="btn-danger" x-data @click="$dispatch('open-modal', 'reject-{{ $s->id }}')">Tolak</button></div>
            <x-modal-confirm :id="'approve-' . $s->id" title="Setujui bukti" :action="route('evidence.approve', $s)" confirm="Setujui & hitung">Bukti ini akan dihitung sebagai evidence operasional. Analisis, attention, dan verifikasi follow-up terkait dijalankan ulang.<label class="label-sm block mt-3 mb-1">Catatan (opsional)</label><textarea name="review_note" rows="2" class="input"></textarea></x-modal-confirm>
            <x-modal-confirm :id="'reject-' . $s->id" title="Tolak bukti" :action="route('evidence.reject', $s)" confirm="Tolak" :danger="true">Bukti tidak akan dihitung sebagai evidence.<label class="label-sm block mt-3 mb-1">Alasan penolakan *</label><textarea name="review_note" rows="2" class="input" required></textarea></x-modal-confirm>
        @endif
    </div>
@empty
    <div class="card"><x-empty title="Tidak ada pengajuan" text="{{ $isDir ? 'Tidak ada bukti dengan status ini.' : 'Anda belum mengajukan bukti. Gunakan tombol Kirim bukti di detail proyek atau follow-up.' }}" /></div>
@endforelse
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
