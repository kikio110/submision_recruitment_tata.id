@extends('layouts.app')
@section('crumb', $project ? 'Projects / ' . $project->project_code . ' / Ubah' : 'Projects / Tambah')
@section('content')
@php $v = fn ($k, $d = null) => old($k, $project ? ($project->$k instanceof \DateTimeInterface ? $project->$k->format($k === 'last_reported_update_at' ? 'Y-m-d\TH:i' : 'Y-m-d') : $project->$k) : $d); @endphp
<x-page-title :title="$project ? 'Ubah proyek ' . $project->project_code : 'Tambah proyek'" :sub="$project ? 'Perubahan disimpan sebagai revisi; nilai lama tetap terlihat sebagai riwayat dan analisis dihitung ulang.' : 'Proyek baru langsung dianalisis memakai aturan yang sama dengan proyek lain.'" />
<form method="POST" action="{{ $project ? route('projects.update', $project) : route('projects.store') }}" class="card card-pad max-w-4xl space-y-4">
    @csrf @if ($project) @method('PUT') @endif
    <div class="grid md:grid-cols-3 gap-4">
        <div><label class="label-sm block mb-1" for="project_id">ID proyek *</label>
            <input id="project_id" name="project_id" value="{{ $project ? $project->project_code : old('project_id') }}" class="input" @disabled($project) required placeholder="mis. PRJ-007">@error('project_id')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div class="md:col-span-2"><label class="label-sm block mb-1" for="project_name">Nama proyek *</label><input id="project_name" name="project_name" value="{{ $v('name', old('project_name')) ?? '' }}" class="input" required>@error('project_name')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="customer">Customer *</label><input id="customer" name="customer" value="{{ $v('customer') }}" class="input" required>@error('customer')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="project_type">Tipe proyek</label><input id="project_type" name="project_type" value="{{ $v('project_type') }}" class="input"></div>
        <div><label class="label-sm block mb-1" for="pic">PM / PIC</label><input id="pic" name="pic" value="{{ $v('pic') }}" class="input" list="pic-list"><datalist id="pic-list">@foreach (\App\Models\Project::whereNotNull('pic')->distinct()->pluck('pic') as $p)<option value="{{ $p }}">@endforeach</datalist></div>
        <div><label class="label-sm block mb-1" for="start_date">Tanggal mulai *</label><input id="start_date" type="date" name="start_date" value="{{ $v('start_date') }}" class="input" required>@error('start_date')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="deadline">Deadline *</label><input id="deadline" type="date" name="deadline" value="{{ $v('deadline') }}" class="input" required>@error('deadline')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div></div>
        <div><label class="label-sm block mb-1" for="target">Target *</label><input id="target" type="number" step="any" name="target" value="{{ $v('target') }}" class="input" required>@error('target')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="target_unit">Satuan target</label><input id="target_unit" name="target_unit" value="{{ $v('target_unit') }}" class="input" placeholder="boxes, pages, records…"></div>
    </div>
    <div class="border-t border-line pt-4"><div class="label-sm mb-2">Kondisi yang dilaporkan (opsional — kosong berarti belum dilaporkan, bukan nol)</div>
        <div class="grid md:grid-cols-4 gap-4">
            <div><label class="label-sm block mb-1" for="reported_actual">Aktual dilaporkan</label><input id="reported_actual" type="number" step="any" name="reported_actual" value="{{ $v('reported_actual') }}" class="input">@error('reported_actual')<div class="field-error">{{ $message }}</div>@enderror</div>
            <div><label class="label-sm block mb-1" for="reported_progress_pct">Progress dilaporkan (%)</label><input id="reported_progress_pct" type="number" step="any" name="reported_progress_pct" value="{{ $v('reported_progress_pct') }}" class="input">@error('reported_progress_pct')<div class="field-error">{{ $message }}</div>@enderror</div>
            <div><label class="label-sm block mb-1" for="reported_status">Status dilaporkan</label><input id="reported_status" name="reported_status" value="{{ $v('reported_status') }}" class="input" list="status-list"><datalist id="status-list">@foreach ($statuses as $s)<option value="{{ $s }}">@endforeach</datalist></div>
            <div><label class="label-sm block mb-1" for="last_reported_update_at">Waktu laporan</label><input id="last_reported_update_at" type="datetime-local" name="last_reported_update_at" value="{{ $v('last_reported_update_at') }}" class="input">@error('last_reported_update_at')<div class="field-error">{{ $message }}</div>@enderror</div>
        </div></div>
    <div class="flex justify-between items-center">
        <div>@if ($project)<button type="button" class="btn-danger" x-data @click="$dispatch('open-modal', 'delete')">Hapus proyek</button>@endif</div>
        <div class="flex gap-2"><a href="{{ $project ? route('projects.show', $project) : route('projects.index') }}" class="btn-secondary">Batal</a><button class="btn-accent">{{ $project ? 'Simpan perubahan' : 'Tambah proyek' }}</button></div>
    </div>
</form>
@if ($project)
    <x-modal-confirm id="delete" title="Hapus proyek" :action="route('projects.destroy', $project)" method="DELETE" confirm="Hapus" :danger="true">Proyek, update, dan evidence-nya akan dihapus permanen. Proyek yang sudah memiliki follow-up tidak dapat dihapus.</x-modal-confirm>
@endif
@endsection
