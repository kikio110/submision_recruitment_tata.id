@extends('layouts.app')
@section('crumb', 'Follow-ups / Buat')
@section('content')
<x-page-title title="Buat follow-up" sub="Pilih proyek dan temuan, lalu tugaskan PIC." />
@php
    $selProject = old('project_id', request('project'));
    $selFinding = old('finding_id', request('finding'));
    $findMap = $findings->map(fn ($c) => $c->map(fn ($f) => ['id' => $f->id, 'title' => "[{$f->severity}] {$f->title}"])->values());
@endphp
<form method="POST" action="{{ route('follow-ups.store') }}" class="card card-pad max-w-3xl space-y-4"
    x-data="{ project: '{{ $selProject }}', finding: '{{ $selFinding }}', map: @js($findMap) , get list() { return this.map[this.project] || []; } }" x-init="$watch('project', () => { if (!list.find(f => f.id == finding)) finding = '' })">
    @csrf
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="label-sm block mb-1" for="project_id">Proyek *</label>
            <select id="project_id" name="project_id" x-model="project" class="input" required><option value="">— pilih proyek —</option>@foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->project_code }} · {{ $p->name }}</option>@endforeach</select>
            @error('project_id')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="finding_id">Temuan terkait</label>
            <select id="finding_id" name="finding_id" x-model="finding" class="input"><option value="">— tanpa temuan —</option><template x-for="f in list" :key="f.id"><option :value="f.id" x-text="f.title" :selected="f.id == finding"></option></template></select>
            <div class="text-[11px] text-ink-500 mt-1">Verifikasi issue hanya berlaku jika follow-up terkait temuan.</div>
            @error('finding_id')<div class="field-error">{{ $message }}</div>@enderror</div>
    </div>
    <div><label class="label-sm block mb-1" for="title">Judul tindakan *</label><input id="title" name="title" value="{{ old('title') }}" class="input" maxlength="200" required>@error('title')<div class="field-error">{{ $message }}</div>@enderror</div>
    <div><label class="label-sm block mb-1" for="description">Deskripsi tindakan</label><textarea id="description" name="description" rows="3" class="input">{{ old('description') }}</textarea>@error('description')<div class="field-error">{{ $message }}</div>@enderror</div>
    <div class="grid md:grid-cols-3 gap-4">
        <div><label class="label-sm block mb-1" for="assigned_pic">PIC *</label><input id="assigned_pic" name="assigned_pic" list="pics" value="{{ old('assigned_pic') }}" class="input" required><datalist id="pics">@foreach ($pics as $p)<option value="{{ $p }}">@endforeach</datalist>@error('assigned_pic')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="priority">Prioritas *</label><select id="priority" name="priority" class="input">@foreach (\App\Models\FollowUp::PRIORITIES as $p)<option value="{{ $p }}" @selected(old('priority', 'MEDIUM') === $p)>{{ ucfirst(strtolower($p)) }}</option>@endforeach</select>@error('priority')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div><label class="label-sm block mb-1" for="due_date">Due date</label><input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}" class="input">@error('due_date')<div class="field-error">{{ $message }}</div>@enderror</div>
    </div>
    <div><label class="label-sm block mb-1" for="instructions">Instruksi tambahan</label><textarea id="instructions" name="instructions" rows="2" class="input">{{ old('instructions') }}</textarea></div>
    <div class="flex justify-end gap-2"><a href="{{ route('follow-ups.index') }}" class="btn-secondary">Batal</a><button class="btn-accent">Simpan &amp; tugaskan</button></div>
</form>
@endsection
