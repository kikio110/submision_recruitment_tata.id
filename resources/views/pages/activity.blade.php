@extends('layouts.app')
@section('crumb', 'Activity History')
@section('content')
<x-page-title title="Activity History" sub="Jejak perubahan follow-up, verifikasi, import, dan tindakan manajemen." />
<form method="GET" class="card card-pad mb-4 flex flex-wrap gap-2 items-end">
    <div><label class="label-sm block mb-1">Cari</label><input name="q" value="{{ request('q') }}" class="input !w-56" placeholder="Kata kunci"></div>
    <div><label class="label-sm block mb-1">Jenis</label><select name="type" class="input !w-52"><option value="">Semua</option>@foreach ($types as $t)<option @selected(request('type') === $t)>{{ $t }}</option>@endforeach</select></div>
    <div><label class="label-sm block mb-1">Proyek</label><select name="project" class="input !w-40"><option value="">Semua</option>@foreach ($projects as $p)<option value="{{ $p->id }}" @selected(request('project') == $p->id)>{{ $p->project_code }}</option>@endforeach</select></div>
    <button class="btn-primary">Filter</button><a href="{{ route('activity.index') }}" class="btn-ghost">Reset</a></form>
<div class="card"><div class="overflow-x-auto"><table class="tbl"><thead><tr><th>Waktu</th><th>Jenis</th><th>Deskripsi</th><th>Aktor</th><th>Proyek</th><th>Follow-up</th></tr></thead><tbody>
@forelse ($logs as $l)<tr><td class="whitespace-nowrap text-[12px]">{{ $l->created_at->format('d M Y H:i:s') }}</td><td><span class="badge bg-well text-ink-700 border-line">{{ $l->type }}</span></td><td class="max-w-xl">{{ $l->description }}</td><td>{{ $l->actor_name }}</td>
<td>@if ($l->project)<a class="text-accent-600" href="{{ route('projects.show', $l->project) }}">{{ $l->project->project_code }}</a>@else—@endif</td><td>@if ($l->followUp)<a class="text-accent-600" href="{{ route('follow-ups.show', $l->followUp) }}">#{{ $l->followUp->id }}</a>@else—@endif</td></tr>
@empty<tr><td colspan="6"><x-empty title="Belum ada aktivitas" /></td></tr>@endforelse</tbody></table></div><div class="p-3 border-t border-line">{{ $logs->links() }}</div></div>
@endsection
