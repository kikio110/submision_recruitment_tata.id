@extends('layouts.app')
@section('crumb', 'Projects / ' . $project->project_code . ' / Laporan')
@section('content')
<x-page-title :title="'Laporan untuk ' . $project->name" :sub="$project->project_code . ' · PIC ' . ($project->pic ?? '—')" />
<x-alert type="info">Laporan Anda tercatat sebagai <b>informasi yang dilaporkan</b> dan muncul di timeline dengan label “Dilaporkan”. Laporan tidak dihitung sebagai bukti operasional, sehingga tidak dapat memverifikasi sebuah issue — itu menunggu observasi dari sistem lain.</x-alert>
<form method="POST" action="{{ route('projects.report', $project) }}" class="card card-pad max-w-3xl space-y-4">@csrf
    <div><label class="label-sm block mb-1" for="message">Isi laporan *</label><textarea id="message" name="message" rows="4" class="input" required placeholder="Apa yang terjadi di lapangan? Sebutkan kendala, kemajuan, atau tindak lanjut.">{{ old('message') }}</textarea>@error('message')<div class="field-error">{{ $message }}</div>@enderror</div>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="label-sm block mb-1" for="reported_at">Waktu laporan *</label><input id="reported_at" type="datetime-local" name="reported_at" value="{{ old('reported_at', $defaultAt) }}" class="input" required><div class="text-[11px] text-ink-500 mt-1">Default mengikuti tanggal acuan aplikasi.</div>@error('reported_at')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="source_type">Pelapor *</label><select id="source_type" name="source_type" class="input">@foreach ($sources as $k => $l)<option value="{{ $k }}" @selected(old('source_type', 'PROJECT_MANAGER') === $k)>{{ $l }}</option>@endforeach</select></div>
    </div>
    <div class="border-t border-line pt-4"><div class="label-sm mb-2">Perbarui progres (opsional)</div>
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="label-sm block mb-1" for="reported_progress_pct">Progress (%)</label><input id="reported_progress_pct" type="number" step="any" name="reported_progress_pct" value="{{ old('reported_progress_pct') }}" class="input" placeholder="saat ini {{ $project->reported_progress_pct ?? 'n/a' }}">@error('reported_progress_pct')<div class="field-error">{{ $message }}</div>@enderror</div>
            <div><label class="label-sm block mb-1" for="reported_actual">Aktual ({{ $project->target_unit ?? 'satuan' }})</label><input id="reported_actual" type="number" step="any" name="reported_actual" value="{{ old('reported_actual') }}" class="input" placeholder="saat ini {{ $project->reported_actual ?? 'n/a' }}"></div>
            <div><label class="label-sm block mb-1" for="reported_status">Status</label><input id="reported_status" name="reported_status" value="{{ old('reported_status') }}" class="input" list="st" placeholder="saat ini {{ $project->reported_status ?? 'n/a' }}"><datalist id="st">@foreach ($statuses as $s)<option value="{{ $s }}">@endforeach</datalist></div>
        </div></div>
    <div class="flex justify-end gap-2"><a href="{{ route('projects.show', $project) }}" class="btn-secondary">Batal</a><button class="btn-accent">Kirim laporan</button></div>
</form>
@endsection
