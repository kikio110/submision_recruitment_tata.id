<div class="card">
    <div class="p-3 border-b border-line flex flex-wrap items-center gap-2">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari ID, nama, atau customer…" class="input !w-64" aria-label="Cari proyek">
        <select wire:model.live="level" class="input !w-auto" aria-label="Filter attention"><option value="">Semua attention</option><option value="HIGH">High</option><option value="MEDIUM">Medium</option><option value="LOW">Low</option></select>
        <select wire:model.live="status" class="input !w-auto" aria-label="Filter status"><option value="">Semua status</option>@foreach ($statuses as $s)<option value="{{ $s }}">{{ ucfirst(strtolower($s)) }}</option>@endforeach</select>
        <select wire:model.live="pic" class="input !w-auto" aria-label="Filter PIC"><option value="">Semua PIC</option>@foreach ($pics as $p)<option>{{ $p }}</option>@endforeach</select>
        @if ($search || $level || $status || $pic)<button wire:click="clear" class="btn-ghost">Reset filter</button>@endif
        <div wire:loading class="text-[12px] text-ink-500">Memuat…</div>
    </div>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr>
                @php $cols = ['project_code' => 'ID', 'name' => 'Proyek', 'pic' => 'PM / PIC', 'reported_progress_pct' => 'Progress', 'deadline' => 'Deadline', 'att_score' => 'Attention', 'open_findings' => 'Temuan', 'latest_update' => 'Update terakhir']; @endphp
                @foreach ($cols as $key => $label)
                    <th><button wire:click="sortBy('{{ $key }}')" class="inline-flex items-center gap-1 hover:text-ink">{{ $label }}@if ($sort === $key)<span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>@endif</button></th>
                @endforeach
                <th>Verifikasi issue</th>
            </tr></thead>
            <tbody>
            @forelse ($projects as $p)
                @php $o = $ctx[$p->id]->observedProgress(); @endphp
                <tr class="cursor-pointer" onclick="window.location='{{ route('projects.show', $p) }}'">
                    <td class="font-mono text-[12px]"><a href="{{ route('projects.show', $p) }}" class="text-accent-600 font-medium">{{ $p->project_code }}</a></td>
                    <td><div class="font-medium">{{ $p->name }}</div><div class="text-[12px] text-ink-500">{{ $p->customer }}</div></td>
                    <td>{{ $p->pic ?? '—' }}</td>
                    <td class="min-w-[170px]">
                        <div class="flex items-center gap-2"><span class="badge bg-well text-ink-700 border-dashed border-slate-400" title="Dilaporkan">👤 {{ $p->reported_progress_pct !== null ? rtrim(rtrim(number_format($p->reported_progress_pct, 1), '0'), '.') . '%' : 'n/a' }}</span>
                        @if ($o)<span class="badge bg-good-bg text-good-fg border-good-bd" title="Observed: {{ $o['label'] }}">🛡 {{ rtrim(rtrim(number_format($o['pct'], 1), '0'), '.') }}%</span>@else<span class="text-[11px] text-ink-500">tanpa observed</span>@endif</div>
                        <div class="h-1 bg-well rounded mt-1.5"><div class="h-1 bg-accent-600 rounded" style="width: {{ min(100, $p->reported_progress_pct ?? 0) }}%"></div></div>
                    </td>
                    <td>{{ $p->deadline?->format('d M Y') ?? '—' }}</td>
                    <td>@if ($p->att_level)<x-badge :value="$p->att_level" /> <span class="text-[12px] text-ink-500 tabular-nums">{{ number_format($p->att_score, 1) }}</span>@else<span class="text-ink-500">—</span>@endif</td>
                    <td class="tabular-nums">{{ $p->open_findings }}</td>
                    <td class="text-[12px] text-ink-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($p->latest_update)->format('d M Y H:i') }}</td>
                    <td>@if ($p->verif_needed > 0)<span class="badge bg-pend-bg text-pend-fg border-pend-bd">{{ $p->verif_needed }} perlu verifikasi</span>@elseif ($p->verif_ok > 0)<x-badge value="VERIFIED" kind="verification" />@else<span class="text-ink-500 text-[12px]">—</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="9"><x-empty title="Tidak ada proyek yang cocok" text="Ubah filter atau pencarian, atau impor data di Data Management." :href="route('data.index')" action="Buka Data Management" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-t border-line flex items-center justify-between text-[12px] text-ink-500">
        <div>{{ $projects->total() }} proyek</div>
        <div>{{ $projects->links(data: ['scrollTo' => false]) }}</div>
    </div>
</div>
