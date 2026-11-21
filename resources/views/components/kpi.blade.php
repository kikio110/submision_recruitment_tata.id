@props(['label', 'value', 'tone' => 'neutral', 'hint' => null, 'href' => null])
@php $bar = ['crit' => 'bg-crit-fg', 'warn' => 'bg-warn-fg', 'good' => 'bg-good-fg', 'pend' => 'bg-pend-fg', 'unv' => 'bg-unv-fg', 'neutral' => 'bg-slate-300'][$tone]; @endphp
<{{ $href ? 'a href='.$href : 'div' }} class="card card-pad relative overflow-hidden block {{ $href ? 'hover:border-slate-300' : '' }}">
    <span class="absolute left-0 top-0 bottom-0 w-1 {{ $bar }}"></span>
    <div class="label-sm">{{ $label }}</div>
    <div class="mt-1 text-[28px] font-bold leading-8 tabular-nums">{{ $value }}</div>
    @if ($hint)<div class="text-[12px] text-ink-500 mt-0.5">{{ $hint }}</div>@endif
</{{ $href ? 'a' : 'div' }}>
