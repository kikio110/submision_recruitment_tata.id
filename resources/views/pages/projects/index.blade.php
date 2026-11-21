@extends('layouts.app')
@section('crumb', 'Projects')
@section('content')
<x-page-title title="Projects" sub="Seluruh portofolio proyek dengan indikator attention dan status verifikasi">
    @if (\App\Support\CurrentUser::get()?->isDirector())<a href="{{ route('projects.create') }}" class="btn-accent">+ Tambah proyek</a>@endif
</x-page-title>
<livewire:portfolio-table />
@endsection
