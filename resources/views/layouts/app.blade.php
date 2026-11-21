<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'OnTrack' }} · OnTrack Project Intelligence</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @livewireStyles
</head>
<body x-data="{ nav: false }">
@php
    $user = \App\Support\CurrentUser::get();
    $asOf = \App\Models\Setting::asOf();
    $items = [
        ['dashboard', 'Overview', 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10', 'dashboard'],
        ['projects.index', 'Projects', 'M3 7h18M3 12h18M3 17h18', 'projects*'],
        ['attention.index', 'Attention Analysis', 'M12 9v4m0 4h.01M10.3 3.9L2.4 18a2 2 0 001.7 3h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z', 'attention*'],
        ['follow-ups.index', 'Follow-ups', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'follow-ups*'],
        ['data.index', 'Data Management', 'M4 7c0-1.1 3.6-2 8-2s8 .9 8 2-3.6 2-8 2-8-.9-8-2zm0 0v10c0 1.1 3.6 2 8 2s8-.9 8-2V7', 'data-management*'],
        ['activity.index', 'Activity History', 'M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z', 'activity*'],
        ['settings.index', 'Settings', 'M10.3 4.3c.4-1.7 3-1.7 3.4 0a1.7 1.7 0 002.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 001 2.6c1.8.4 1.8 3 0 3.4a1.7 1.7 0 00-1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 00-2.6 1c-.4 1.8-3 1.8-3.4 0a1.7 1.7 0 00-2.6-1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 00-1-2.6c-1.8-.4-1.8-3 0-3.4a1.7 1.7 0 001-2.6c-.9-1.5.9-3.3 2.4-2.4a1.7 1.7 0 002.6-1z', 'settings*'],
    ];
@endphp
<div class="min-h-screen lg:flex">
    <aside class="bg-navy-900 text-slate-300 lg:w-[232px] lg:fixed lg:inset-y-0 lg:flex lg:flex-col z-30" >
        <div class="flex items-center justify-between h-14 px-4 border-b border-white/10">
            <div class="flex items-center gap-2"><div class="w-7 h-7 rounded bg-accent-600 text-white grid place-items-center font-bold text-[13px]">O</div>
                <div class="leading-tight"><div class="text-white font-semibold text-[14px]">OnTrack</div><div class="text-[10px] uppercase tracking-widest text-slate-400">Project Intelligence</div></div></div>
            <button class="lg:hidden text-slate-300" @click="nav = !nav" aria-label="Menu">☰</button>
        </div>
        <nav class="px-2 py-3 space-y-0.5 lg:flex-1" :class="nav ? 'block' : 'hidden lg:block'">
            @foreach ($items as [$route, $label, $icon, $pattern])
                <a href="{{ route($route) }}" class="flex items-center gap-2.5 px-3 h-9 rounded text-[13px] font-medium {{ request()->is($pattern) ? 'bg-navy-800 text-white' : 'hover:bg-navy-800/60 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="hidden lg:block px-4 py-3 border-t border-white/10 text-[11px] text-slate-400">Tanggal acuan data<br><span class="text-slate-200 font-medium">{{ $asOf->format('d M Y, H:i') }}</span></div>
    </aside>

    <div class="flex-1 lg:ml-[232px] min-w-0">
        <header class="h-14 bg-white border-b border-line flex items-center justify-between px-4 lg:px-6 sticky top-0 z-20">
            <div class="text-[13px] text-ink-500">@yield('crumb', 'OnTrack')</div>
            <form method="POST" action="{{ route('user.switch') }}" class="flex items-center gap-2">
                @csrf
                <span class="label-sm hidden sm:inline">Demo user</span>
                <select name="user_id" class="input !w-auto !h-8" onchange="this.form.submit()" aria-label="Pilih demo user">
                    @foreach (\App\Models\User::orderBy('id')->get() as $u)
                        <option value="{{ $u->id }}" @selected($user?->id === $u->id)>{{ $u->name }} — {{ $u->role === 'DIRECTOR' ? 'Director' : 'Project Manager' }}</option>
                    @endforeach
                </select>
            </form>
        </header>

        <main class="px-4 lg:px-6 py-5 max-w-page mx-auto">
            @if (session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
            @if (session('error'))<x-alert type="error">{{ session('error') }}</x-alert>@endif
            @if ($errors->any() && ! request()->routeIs('follow-ups.create'))<x-alert type="error">Periksa kembali isian: {{ $errors->first() }}</x-alert>@endif
            @yield('content')
        </main>
    </div>
</div>
@livewireScripts
@stack('scripts')
</body>
</html>
