<div class="card">
    <div class="p-3 border-b border-line flex flex-wrap items-center gap-2">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari judul, PIC, proyek…" class="input !w-64" aria-label="Cari follow-up">
        <select wire:model.live="status" class="input !w-auto"><option value="">Semua status tindakan</option>@foreach (\App\Models\FollowUp::STATUSES as $s)<option value="{{ $s }}">{{ ucfirst(strtolower(str_replace('_', ' ', $s))) }}</option>@endforeach</select>
        <select wire:model.live="priority" class="input !w-auto"><option value="">Semua prioritas</option>@foreach (\App\Models\FollowUp::PRIORITIES as $s)<option value="{{ $s }}">{{ ucfirst(strtolower($s)) }}</option>@endforeach</select>
        <select wire:model.live="issue" class="input !w-auto"><option value="">Semua status issue</option>@foreach (['OPEN', 'PENDING_VERIFICATION', 'VERIFIED', 'UNVERIFIED', 'REOPENED'] as $s)<option value="{{ $s }}">{{ ucfirst(strtolower(str_replace('_', ' ', $s))) }}</option>@endforeach</select>
        @unless ($isDirector)<span class="text-[12px] text-ink-500">Menampilkan follow-up yang ditugaskan kepada Anda.</span>@endunless
    </div>
    <div class="overflow-x-auto"><table class="tbl">
        <thead><tr>
            @foreach (['title' => 'Tindakan', 'assigned_pic' => 'PIC', 'priority' => 'Prioritas', 'due_date' => 'Due date', 'status' => 'Status tindakan'] as $k => $l)
                <th><button wire:click="sortBy('{{ $k }}')">{{ $l }} @if ($sort === $k){{ $dir === 'asc' ? '▲' : '▼' }}@endif</button></th>@endforeach
            <th>Status issue</th></tr></thead>
        <tbody>
        @forelse ($items as $f)
            <tr class="cursor-pointer" onclick="window.location='{{ route('follow-ups.show', $f) }}'">
                <td><a class="font-medium text-accent-600" href="{{ route('follow-ups.show', $f) }}">{{ $f->title }}</a><div class="text-[12px] text-ink-500">{{ $f->project->project_code }} · {{ $f->project->name }}</div></td>
                <td>{{ $f->assigned_pic }}</td>
                <td><x-badge :value="$f->priority" kind="priority" /></td>
                <td class="whitespace-nowrap">{{ $f->due_date?->format('d M Y') ?? '—' }} @if ($f->isOverdue())<span class="badge bg-crit-bg text-crit-fg border-crit-bd">Overdue</span>@endif</td>
                <td><x-badge :value="$f->status" kind="followup" /></td>
                <td>@if ($f->finding)<x-badge :value="$f->finding->verification_status" kind="verification" />@else<span class="text-ink-500 text-[12px]">tanpa temuan</span>@endif</td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty title="Belum ada follow-up" text="Follow-up dibuat Director dari temuan sebuah proyek." :href="$isDirector ? route('follow-ups.create') : null" action="Buat follow-up" /></td></tr>
        @endforelse
        </tbody></table></div>
    <div class="p-3 border-t border-line text-[12px] text-ink-500 flex justify-between"><div>{{ $items->total() }} follow-up</div><div>{{ $items->links(data: ['scrollTo' => false]) }}</div></div>
</div>
