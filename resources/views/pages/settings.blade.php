@extends('layouts.app')
@section('crumb', 'Settings')
@section('content')
<x-page-title title="Settings" sub="Tanggal acuan dan parameter interpretasi (keputusan desain, bukan fakta data — SRS A-04)." />
<div class="grid lg:grid-cols-2 gap-4">
    <form method="POST" action="{{ route('settings.update') }}" class="card card-pad">@csrf
        <h2 class="mb-1">Tanggal acuan (as-of)</h2><p class="text-[13px] text-ink-500 mb-3">Interpretasi memakai tanggal ini, bukan jam sistem. Mengubahnya menghitung ulang semua temuan dan attention.</p>
        <input type="datetime-local" name="as_of" value="{{ old('as_of', $asOf->format('Y-m-d\TH:i')) }}" class="input !w-64" required>@error('as_of')<div class="field-error">{{ $message }}</div>@enderror
        <div class="mt-3"><button class="btn-accent" onclick="return confirm('Ubah tanggal acuan dan hitung ulang semua interpretasi?')" @disabled(! \App\Support\CurrentUser::get()?->isDirector())>Simpan &amp; hitung ulang</button></div></form>
    <div class="card card-pad"><h2 class="mb-2">Bobot attention</h2>@foreach ($weights as $k => $w)<div class="flex justify-between text-[13px] py-1 border-b border-well"><span>{{ ucwords(str_replace('_', ' ', $k)) }}</span><b>{{ round($w * 100) }}%</b></div>@endforeach
        <p class="text-[12px] text-ink-500 mt-2">Level: HIGH ≥ {{ $levels['HIGH'] }}, MEDIUM ≥ {{ $levels['MEDIUM'] }}; aturan lantai berlaku untuk temuan CRITICAL/HIGH/MEDIUM.</p></div>
    <div class="card card-pad lg:col-span-2"><h2 class="mb-2">Ambang interpretasi</h2><div class="grid md:grid-cols-2 gap-x-8">@foreach ($thresholds as $k => $v)@php [$label, $unit] = $labels[$k] ?? [ucfirst(str_replace('_', ' ', $k)), ''];@endphp<div class="flex justify-between gap-3 text-[13px] py-1.5 border-b border-well"><span class="text-ink-700">{{ $label }}<span class="block text-[11px] text-ink-500">{{ $unit }}</span></span><b class="tabular-nums">{{ $v }}</b></div>@endforeach</div></div>
    <div class="card card-pad lg:col-span-2"><h2 class="mb-1">Keputusan desain &amp; kondisi tinjau ulang</h2><p class="text-[13px] text-ink-500 mb-3">Aturan prioritas, model data, dan cara menjelaskan ketidakpastian ditetapkan oleh tim produk. Berikut alasan setiap keputusan dan kapan harus ditinjau ulang.</p>
        @foreach ($decisions as $d)
            <details class="border border-line rounded-lg mb-2 group" @if ($loop->first) open @endif><summary class="cursor-pointer px-3 py-2.5 font-medium text-[13px] flex justify-between">{{ $d['topic'] }}<span class="text-ink-500 group-open:rotate-180">⌄</span></summary>
                <dl class="px-3 pb-3 grid md:grid-cols-2 gap-x-6 gap-y-2 text-[13px]">
                    <div><dt class="label-sm">Keputusan</dt><dd>{{ $d['decision'] }}</dd></div><div><dt class="label-sm">Alasan</dt><dd>{{ $d['why'] }}</dd></div>
                    <div><dt class="label-sm">Penanganan ketidakpastian</dt><dd>{{ $d['uncertainty'] }}</dd></div><div><dt class="label-sm">Tinjau ulang bila</dt><dd>{{ $d['review'] }}</dd></div></dl></details>
        @endforeach</div>
</div>
@endsection
