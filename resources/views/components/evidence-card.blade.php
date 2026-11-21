@props(['e', 'role' => null])
@php $op = $e->evidence_class === 'OPERATIONAL'; @endphp
<div class="border rounded-lg p-3 {{ $op ? 'border-line bg-white' : 'border-dashed border-slate-300 bg-canvas' }} {{ $e->is_current ? '' : 'opacity-75' }}" id="evidence-{{ $e->id }}">
    <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
        @if ($op)
            <span class="badge bg-good-bg text-good-fg border-good-bd">🛡 Operational evidence</span>
        @else
            <span class="badge bg-well text-ink-700 border-dashed border-slate-400">👤 Reported</span>
        @endif
        <span class="badge bg-white text-ink-700 border-line">{{ $e->category }}</span>
        @if ($role === 'CONTRAST')<span class="badge bg-warn-bg text-warn-fg border-warn-bd">Pembanding</span>@endif
        @if (! $e->is_current)<span class="badge bg-well text-ink-500 border-line">Riwayat (superseded)</span>@endif
        @if ($e->is_stale)<span class="badge bg-warn-bg text-warn-fg border-warn-bd">Stale</span>@endif
        @if ($e->is_duplicate)<span class="badge bg-well text-ink-700 border-line">Duplikat sumber</span>@endif
        @if ($e->conflict_group)<span class="badge bg-crit-bg text-crit-fg border-crit-bd">⚠ Conflicting</span>@endif
        @if ($e->revision > 1)<span class="badge bg-pend-bg text-pend-fg border-pend-bd">Rev {{ $e->revision }}</span>@endif
    </div>
    <div class="text-[13px] {{ $e->metric_key === 'update_message' ? '' : 'font-medium' }}">{{ $e->summary }}</div>
    <dl class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-1 text-[12px]">
        <div><dt class="label-sm">Sumber</dt><dd class="text-ink-700">{{ $e->sourceText() }}</dd></div>
        <div><dt class="label-sm">Referensi</dt><dd class="font-mono text-[11px] text-ink-700">{{ $e->source_ref }}</dd></div>
        <div><dt class="label-sm">Timestamp</dt><dd class="text-ink-700">{{ $e->observed_at->format('d M Y H:i') }}</dd></div>
        <div><dt class="label-sm">Nilai teramati</dt><dd class="text-ink-700">{{ $e->metric_key === 'update_message' ? 'pesan teks' : ($e->observed_value ?? 'null') . ($e->unit ? ' ' . $e->unit : '') }}@if ($e->subject_key && $e->metric_key !== 'update_message') <span class="text-ink-500">({{ $e->subject_key }})</span>@endif</dd></div>
    </dl>
    @if ($e->signals)<div class="mt-1.5 flex gap-1 flex-wrap">@foreach ($e->signalList() as $s)<span class="badge bg-white text-ink-500 border-line">{{ $s }}</span>@endforeach</div>@endif
</div>
