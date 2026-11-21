@extends('layouts.app')
@section('crumb', 'Projects / ' . $project->project_code . ' / Kirim bukti')
@section('content')
@php $isDir = \App\Support\CurrentUser::get()?->isDirector(); @endphp
<x-page-title title="Kirim bukti operasional" :sub="$project->project_code . ' · ' . $project->name . ($followUp ? ' · untuk follow-up #' . $followUp->id : '')" />
@if ($isDir)
    <x-alert type="info">Sebagai Director, bukti yang Anda kirim <b>langsung disetujui</b> dan dihitung sebagai evidence operasional.</x-alert>
@else
    <x-alert type="info">Bukti operasional adalah <b>observasi dari sistem lain</b> (log, monitor, dashboard), bukan pernyataan. Pengajuan Anda akan ditinjau Director dan <b>baru dihitung setelah disetujui</b>. Sebelum itu, issue tidak dapat menjadi VERIFIED.</x-alert>
@endif
<form method="POST" action="{{ route('evidence.store', $project) }}" class="card card-pad max-w-3xl space-y-4" x-data="{ metric: '{{ old('metric_key', $pre['metric_key']) }}' }">@csrf
    @if ($followUp)<input type="hidden" name="follow_up_id" value="{{ $followUp->id }}">
        <div class="bg-canvas border border-line rounded p-3 text-[13px]"><div class="label-sm">Issue asal</div>{{ $followUp->finding?->title ?? $followUp->title }}</div>@endif
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="label-sm block mb-1" for="metric_key">Yang diamati *</label>
            <select id="metric_key" name="metric_key" x-model="metric" class="input" required><option value="">— pilih —</option>@foreach ($metrics as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select>@error('metric_key')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div x-show="metric === 'scanner_status'" x-cloak><label class="label-sm block mb-1" for="subject_key">Subjek (ID perangkat) *</label><input id="subject_key" name="subject_key" value="{{ old('subject_key', $pre['subject_key']) }}" class="input" placeholder="mis. SCN-B">@error('subject_key')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="observed_value">Nilai teramati *</label><input id="observed_value" name="observed_value" value="{{ old('observed_value') }}" class="input" placeholder="angka, atau status mis. RUNNING / CONNECTED" required>@error('observed_value')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="unit">Satuan</label><input id="unit" name="unit" value="{{ old('unit') }}" class="input" placeholder="opsional, mis. pages"></div>
        <div><label class="label-sm block mb-1" for="observed_at">Waktu observasi *</label><input id="observed_at" type="datetime-local" name="observed_at" value="{{ old('observed_at', $asOf->format('Y-m-d\TH:i')) }}" max="{{ $asOf->format('Y-m-d\TH:i') }}" class="input" required><div class="text-[11px] text-ink-500 mt-1">Kapan sistem asal mencatatnya (bukan kapan Anda mengirim). Maksimal sampai tanggal acuan.</div>@error('observed_at')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="source_system">Sistem asal *</label><input id="source_system" name="source_system" value="{{ old('source_system') }}" class="input" placeholder="mis. scanner_production_log" required>@error('source_system')<div class="field-error">{{ $message }}</div>@enderror</div>
    </div>
    <div><label class="label-sm block mb-1" for="source_note">Asal bukti *</label><textarea id="source_note" name="source_note" rows="3" class="input" placeholder="Bagaimana bukti ini didapat? Mis. tangkapan layar monitor jam 09:00, nomor tiket, nama file log." required>{{ old('source_note') }}</textarea>@error('source_note')<div class="field-error">{{ $message }}</div>@enderror</div>
    <div class="flex justify-end gap-2"><a href="{{ $followUp ? route('follow-ups.show', $followUp) : route('projects.show', $project) }}" class="btn-secondary">Batal</a><button class="btn-accent">{{ $isDir ? 'Simpan sebagai evidence' : 'Ajukan untuk ditinjau' }}</button></div>
</form>
@endsection
