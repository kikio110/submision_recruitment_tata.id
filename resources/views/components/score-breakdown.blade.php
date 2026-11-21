@props(['assessment'])
<div class="space-y-3">
    @foreach ($assessment->criteria as $c)
        @php $unknown = $c['status'] === 'UNKNOWN'; $s = $c['score'] ?? 0;
             $color = $unknown ? 'bg-slate-300' : ($s >= 70 ? 'bg-crit-fg' : ($s >= 35 ? 'bg-warn-fg' : 'bg-good-fg')); @endphp
        <div>
            <div class="flex items-center justify-between text-[13px]">
                <div class="font-medium">{{ $c['label'] }} <span class="text-ink-500 font-normal">· bobot {{ round($c['weight'] * 100) }}%</span></div>
                <div class="tabular-nums font-semibold">@if ($unknown)<span class="badge bg-unv-bg text-unv-fg border-unv-bd">UNKNOWN</span>@else {{ number_format($s, 1) }}<span class="text-ink-500 font-normal"> / 100 → +{{ $c['contribution'] }} poin</span>@endif</div>
            </div>
            <div class="h-1.5 bg-well rounded mt-1.5 overflow-hidden"><div class="h-full {{ $color }}" style="width: {{ $unknown ? 100 : $s }}%; {{ $unknown ? 'opacity:.35' : '' }}"></div></div>
            <div class="text-[12px] text-ink-500 mt-1">{{ $c['reason'] }}</div>
        </div>
    @endforeach
</div>
