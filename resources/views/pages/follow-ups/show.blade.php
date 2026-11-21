@extends('layouts.app')
@section('crumb', 'Follow-ups / #' . $f->id)
@section('content')
@php $fd = $f->finding; $labels = ['OPEN' => 'Buka kembali', 'IN_PROGRESS' => 'Mulai / lanjutkan pengerjaan', 'RESOLVED' => 'Tandai RESOLVED', 'CANCELLED' => 'Batalkan']; @endphp
<x-page-title :title="$f->title" :sub="$f->project->project_code . ' · ' . $f->project->name">
    <a href="{{ route('projects.show', $f->project) }}" class="btn-secondary">Detail proyek</a>
</x-page-title>

@unless ($canModify)<x-alert type="info">Mode lihat saja: follow-up ini ditugaskan kepada {{ $f->assigned_pic }}.</x-alert>@endunless

<div class="grid xl:grid-cols-3 gap-4">
<div class="xl:col-span-2 space-y-4">
    <div class="card card-pad">
        <div class="flex flex-wrap items-center gap-2 mb-3"><x-badge :value="$f->status" kind="followup" /><x-badge :value="$f->priority" kind="priority" />@if ($f->isOverdue())<span class="badge bg-crit-bg text-crit-fg border-crit-bd">Overdue</span>@endif</div>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-[13px]">
            <div><dt class="label-sm">PIC</dt><dd class="font-medium">{{ $f->assigned_pic }}</dd></div><div><dt class="label-sm">Due date</dt><dd class="font-medium">{{ $f->due_date?->format('d M Y') ?? '—' }}</dd></div>
            <div><dt class="label-sm">Dibuat oleh</dt><dd>{{ $f->creator?->name ?? '—' }}<div class="text-[11px] text-ink-500">{{ $f->created_at->format('d M Y H:i') }}</div></dd></div>
            <div><dt class="label-sm">Diperbarui oleh</dt><dd>{{ $f->updater?->name ?? '—' }}<div class="text-[11px] text-ink-500">{{ $f->updated_at->format('d M Y H:i') }}</div></dd></div>
        </dl>
        @if ($f->description)<div class="mt-3"><div class="label-sm">Deskripsi</div><p class="text-[13px] whitespace-pre-line">{{ $f->description }}</p></div>@endif
        @if ($f->instructions)<div class="mt-3"><div class="label-sm">Instruksi tambahan</div><p class="text-[13px] whitespace-pre-line">{{ $f->instructions }}</p></div>@endif
        @if ($canModify)
        <div class="mt-4 pt-3 border-t border-line flex flex-wrap gap-2">
            @foreach ($transitions as $t)
                <button type="button" class="{{ $t === 'RESOLVED' ? 'btn-accent' : ($t === 'CANCELLED' ? 'btn-danger' : 'btn-secondary') }}" x-data @click="$dispatch('open-modal', 'status-{{ $t }}')">{{ $labels[$t] }}</button>
                <x-modal-confirm id="status-{{ $t }}" :title="$labels[$t]" :action="route('follow-ups.status', $f)" :danger="$t === 'CANCELLED'">
                    <input type="hidden" name="status" value="{{ $t }}">
                    @if ($t === 'RESOLVED')<p class="mb-2">Menandai RESOLVED hanya berarti <b>tindakan selesai</b>. Sistem akan mengevaluasi evidence terbaru; issue baru VERIFIED bila evidence operasional membuktikannya.</p>@else<p class="mb-2">Ubah status dari <b>{{ $f->status }}</b> ke <b>{{ $t }}</b>?</p>@endif
                    <label class="label-sm block mb-1">Catatan (opsional)</label><textarea name="note" rows="2" class="input"></textarea>
                </x-modal-confirm>
            @endforeach
            <button type="button" class="btn-ghost ml-auto" x-data @click="$dispatch('open-modal', 'edit')">Ubah detail</button>
        </div>
        @endif
    </div>

    {{-- Verification panel --}}
    <div class="card card-pad" id="verification">
        <h2 class="mb-3">Verifikasi issue</h2>
        <div class="grid md:grid-cols-2 gap-3 mb-3">
            <div class="border border-line rounded-lg p-3"><div class="label-sm">Status follow-up (tindakan)</div><div class="mt-1"><x-badge :value="$f->status" kind="followup" /></div><div class="text-[11px] text-ink-500 mt-1">Menjawab: apakah tindakan sudah dikerjakan?</div></div>
            <div class="border border-line rounded-lg p-3"><div class="label-sm">Status issue (verifikasi)</div><div class="mt-1">@if ($fd)<x-badge :value="$fd->verification_status" kind="verification" />@else<span class="text-ink-500">Tidak terkait temuan</span>@endif</div><div class="text-[11px] text-ink-500 mt-1">Menjawab: apakah evidence membuktikan masalah selesai?</div></div>
        </div>
        @if ($fd)
            <div class="mb-3"><div class="label-sm">Issue asal</div><div class="flex items-center gap-2 mt-1"><x-badge :value="$fd->severity" kind="severity" /><span class="font-medium">{{ $fd->title }}</span></div><p class="text-[13px] text-ink-700 mt-1">{{ $fd->description }}</p><div class="text-[11px] text-ink-500 mt-1">Baseline evidence: {{ $fd->baseline_at?->format('d M Y H:i') ?? '—' }}</div></div>
            @if ($verification)
                <div class="grid md:grid-cols-2 gap-3 mb-3">
                    <div><div class="label-sm mb-1">Evidence sebelumnya</div>@if ($verification->previousEvidence)<x-evidence-card :e="$verification->previousEvidence" />@else<div class="text-[12px] text-ink-500">Tidak ada.</div>@endif</div>
                    <div><div class="label-sm mb-1">Evidence terbaru</div>@if ($verification->latestEvidence)<x-evidence-card :e="$verification->latestEvidence" />@else<div class="border border-dashed border-unv-bd bg-unv-bg rounded-lg p-3 text-[12px] text-unv-fg">Belum ada evidence operasional yang lebih baru dari baseline.</div>@endif</div>
                </div>
                <div class="border border-line rounded-lg p-3 bg-canvas"><div class="flex items-center gap-2"><x-badge :value="$verification->result" kind="verification" /><span class="text-[11px] text-ink-500">{{ $verification->created_at->format('d M Y H:i') }} · pemicu {{ str_replace('_', ' ', strtolower($verification->trigger)) }} · as-of {{ $verification->as_of->format('d M Y H:i') }}</span></div>
                    <p class="text-[13px] mt-1.5">{{ $verification->explanation }}</p></div>
            @else
                <div class="border border-dashed border-line rounded-lg p-3 text-[13px] text-ink-500">Belum pernah diverifikasi. Verifikasi berjalan otomatis saat follow-up ditandai RESOLVED atau data di-refresh.</div>
            @endif
            @if ($canModify)<div class="mt-3 flex flex-wrap gap-2"><button type="button" class="btn-secondary" x-data @click="$dispatch('open-modal', 'verify')">↻ Verify again</button><a href="{{ route('evidence.create', ['project' => $f->project_id, 'follow_up' => $f->id]) }}" class="btn-accent">Kirim bukti operasional</a></div>
                <p class="text-[11px] text-ink-500 mt-1.5">Untuk memverifikasi, perlu evidence operasional yang lebih baru. Bukti yang Anda kirim ditinjau Director; setelah disetujui, verifikasi ulang berjalan otomatis.</p>
                <x-modal-confirm id="verify" title="Verifikasi ulang issue" :action="route('follow-ups.verify', $f)" confirm="Verifikasi sekarang">Sistem akan mengevaluasi ulang evidence operasional terbaru terhadap temuan asal.</x-modal-confirm>@endif
        @endif
    </div>

    @if ($f->evidenceSubmissions->isNotEmpty())
    <div class="card card-pad"><h2 class="mb-2">Bukti yang diajukan untuk follow-up ini</h2>
        @foreach ($f->evidenceSubmissions as $sb)<div class="py-2 border-b border-well last:border-0 text-[13px]"><span class="badge {{ ['PENDING' => 'bg-pend-bg text-pend-fg border-pend-bd', 'APPROVED' => 'bg-good-bg text-good-fg border-good-bd', 'REJECTED' => 'bg-crit-bg text-crit-fg border-crit-bd'][$sb->status] }}">{{ ['PENDING' => 'Menunggu tinjauan', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'][$sb->status] }}</span> {{ $sb->metricLabel() }}@if ($sb->subject_key) ({{ $sb->subject_key }})@endif = <b>{{ $sb->observed_value }}</b> <span class="text-[11px] text-ink-500">· {{ $sb->observed_at->format('d M Y H:i') }} · {{ $sb->source_system }}</span>@if ($sb->review_note)<div class="text-[12px] text-ink-700 mt-0.5">Tinjauan: {{ $sb->review_note }}</div>@endif</div>@endforeach</div>
    @endif

    @if ($fd && $fd->verifications->count() > 1)
    <div class="card card-pad"><h2 class="mb-2">Riwayat verifikasi</h2>
        @foreach ($fd->verifications as $v)<div class="py-2 border-b border-well last:border-0 text-[13px]"><x-badge :value="$v->result" kind="verification" /> <span class="text-[11px] text-ink-500">{{ $v->created_at->format('d M Y H:i') }} · {{ $v->trigger }}</span><div class="text-ink-700 mt-0.5">{{ $v->explanation }}</div></div>@endforeach</div>
    @endif
</div>

<div class="space-y-4">
    @if ($canModify)
    <form method="POST" action="{{ route('follow-ups.note', $f) }}" class="card card-pad">@csrf
        <h2 class="mb-2">Tambah catatan progres</h2><textarea name="note" rows="3" class="input" placeholder="Apa yang sudah dikerjakan?" required>{{ old('note') }}</textarea>@error('note')<div class="field-error">{{ $message }}</div>@enderror
        <label class="flex items-start gap-2 mt-2 text-[12px] text-ink-700"><input type="checkbox" name="report_to_timeline" value="1" class="mt-0.5" @checked(old('report_to_timeline'))><span>Laporkan juga ke timeline proyek <span class="text-ink-500">(tercatat sebagai informasi yang dilaporkan, bukan bukti operasional)</span></span></label>
        <div class="mt-2 text-right"><button class="btn-primary">Simpan catatan</button></div></form>
    @endif
    <div class="card card-pad"><h2 class="mb-3">Riwayat aktivitas</h2>
        <ol class="relative border-l border-line ml-2 space-y-3">
            @foreach ($f->histories as $h)
                <li class="pl-4 relative"><span class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-accent-600"></span>
                    <div class="text-[11px] text-ink-500">{{ $h->created_at->format('d M Y H:i') }} · {{ $h->actor_name ?? 'System' }}</div>
                    <div class="text-[13px] font-medium">{{ str_replace('_', ' ', ucfirst(strtolower($h->event))) }}@if ($h->from_value || $h->to_value) <span class="text-ink-500 font-normal">{{ $h->from_value }}{{ $h->from_value && $h->to_value ? ' → ' : '' }}{{ $h->to_value }}</span>@endif</div>
                    @if ($h->note)<div class="text-[12px] text-ink-700 whitespace-pre-line">{{ $h->note }}</div>@endif</li>
            @endforeach
        </ol></div>
</div>
</div>

@if ($canModify)
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'edit') open = true" x-on:keydown.escape.window="open = false" x-show="open" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4" style="display:none">
    <div class="absolute inset-0 bg-navy-900/50" @click="open = false"></div>
    <form method="POST" action="{{ route('follow-ups.update', $f) }}" class="relative bg-white rounded-lg border border-line shadow-float w-full max-w-lg p-5 space-y-3 max-h-[90vh] overflow-y-auto">@csrf @method('PUT')
        <h2>Ubah detail follow-up</h2>
        <div><label class="label-sm block mb-1">Judul *</label><input name="title" value="{{ old('title', $f->title) }}" class="input" required></div>
        <div><label class="label-sm block mb-1">Deskripsi</label><textarea name="description" rows="3" class="input">{{ old('description', $f->description) }}</textarea></div>
        <div><label class="label-sm block mb-1">Instruksi tambahan</label><textarea name="instructions" rows="2" class="input">{{ old('instructions', $f->instructions) }}</textarea></div>
        <div class="grid grid-cols-3 gap-3">
            @if (\App\Support\CurrentUser::get()?->isDirector())<div><label class="label-sm block mb-1">PIC *</label><input name="assigned_pic" list="pics2" value="{{ old('assigned_pic', $f->assigned_pic) }}" class="input" required><datalist id="pics2">@foreach ($pics as $p)<option value="{{ $p }}">@endforeach</datalist></div>@endif
            <div><label class="label-sm block mb-1">Prioritas</label><select name="priority" class="input">@foreach (\App\Models\FollowUp::PRIORITIES as $p)<option value="{{ $p }}" @selected($f->priority === $p)>{{ ucfirst(strtolower($p)) }}</option>@endforeach</select></div>
            <div><label class="label-sm block mb-1">Due date</label><input type="date" name="due_date" value="{{ old('due_date', $f->due_date?->toDateString()) }}" class="input"></div></div>
        <div class="flex justify-end gap-2"><button type="button" class="btn-secondary" @click="open = false">Batal</button><button class="btn-accent">Simpan</button></div>
    </form>
</div>
@endif
@endsection
