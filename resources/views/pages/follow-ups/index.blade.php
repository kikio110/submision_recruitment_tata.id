@extends('layouts.app')
@section('crumb', 'Follow-ups')
@section('content')
<x-page-title title="Follow-ups" sub="Tindakan yang ditugaskan. Status tindakan terpisah dari status issue (verifikasi evidence).">
    <a href="{{ route('evidence.index') }}" class="btn-secondary">Tinjauan bukti @if (($n = \App\Services\EvidenceSubmissionService::pendingCount()) > 0)<span class="badge bg-pend-bg text-pend-fg border-pend-bd">{{ $n }}</span>@endif</a>
    @if (\App\Support\CurrentUser::get()?->isDirector())<a href="{{ route('follow-ups.create') }}" class="btn-accent">+ Buat follow-up</a>@endif
</x-page-title>
<livewire:follow-up-table />
@endsection
