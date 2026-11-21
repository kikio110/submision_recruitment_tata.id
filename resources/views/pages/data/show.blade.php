@extends('layouts.app')
@section('crumb', 'Data Management / Batch #' . $batch->id)
@section('content')
<x-page-title :title="'Batch #' . $batch->id . ' — ' . $batch->file_name" :sub="str_replace('_', ' ', $batch->source_kind ?? 'jenis tidak dikenali') . ' · as-of ' . $batch->as_of->format('d M Y H:i')"><a href="{{ route('data.imports') }}" class="btn-secondary">Riwayat import</a></x-page-title>
<div class="card card-pad mb-4"><div class="flex items-center gap-2 mb-2"><x-badge :value="$batch->status" kind="import" /><span class="text-[13px]">{{ $batch->message }}</span></div>
    <div class="grid grid-cols-3 md:grid-cols-7 gap-2 text-center">
        @foreach (['total_records' => 'Total', 'valid_records' => 'Valid', 'invalid_records' => 'Invalid', 'missing_field_count' => 'Field kosong', 'duplicate_records' => 'Duplikat', 'conflicting_records' => 'Konflik/revisi', 'new_records' => 'Baru'] as $k => $l)<div class="bg-canvas rounded p-2"><div class="text-[20px] font-bold tabular-nums">{{ $batch->$k }}</div><div class="label-sm">{{ $l }}</div></div>@endforeach</div></div>
@if ($batch->impact)
<div class="card card-pad mb-4"><h2 class="mb-1">Dampak terhadap analisis</h2><p class="text-[12px] text-ink-500 mb-3">Perbandingan sebelum dan sesudah perubahan ini diproses. {{ $batch->impact['unchanged'] ?? 0 }} proyek yang terkait tidak berubah hasil analisisnya.</p>
    @forelse ($batch->impact['projects'] ?? [] as $p)
        <div class="border border-line rounded-lg p-3 mb-2"><div class="flex flex-wrap items-center gap-2"><span class="font-medium">{{ $p['code'] }} · {{ $p['name'] }}</span>@if ($p['new_project'])<span class="badge bg-pend-bg text-pend-fg border-pend-bd">Proyek baru</span>@endif
            @if ($p['level_after'])<span class="text-[12px] text-ink-500">Attention:</span>@if ($p['level_before'] && ! $p['new_project'])<x-badge :value="$p['level_before']" /><span>→</span>@endif<x-badge :value="$p['level_after']" /><span class="text-[12px] tabular-nums text-ink-500">skor {{ $p['score_before'] !== null ? number_format($p['score_before'], 1) . ' → ' : '' }}{{ number_format($p['score_after'], 1) }}</span>@endif</div>
            @foreach (['opened' => ['Temuan baru', 'text-crit-fg'], 'closed' => ['Temuan tidak terdeteksi lagi', 'text-good-fg'], 'changed' => ['Temuan berubah', 'text-warn-fg']] as $k => [$label, $c])
                @if (! empty($p[$k]))<div class="mt-1.5 text-[12px]"><span class="font-semibold {{ $c }}">{{ $label }}:</span><ul class="list-disc ml-5 text-ink-700">@foreach ($p[$k] as $t)<li>{{ $t }}</li>@endforeach</ul></div>@endif
            @endforeach</div>
    @empty<div class="text-[13px] text-ink-500">Perubahan ini tidak mengubah level, skor, maupun temuan proyek mana pun.</div>@endforelse</div>
@endif
<div class="card"><div class="p-3 border-b border-line font-semibold">Laporan kualitas data</div><div class="overflow-x-auto"><table class="tbl"><thead><tr><th>Severity</th><th>Kode</th><th>Record</th><th>Field</th><th>Pesan</th></tr></thead><tbody>
@forelse ($issues as $e)<tr><td><span class="badge {{ ['ERROR' => 'bg-crit-bg text-crit-fg border-crit-bd', 'WARNING' => 'bg-warn-bg text-warn-fg border-warn-bd', 'INFO' => 'bg-well text-ink-700 border-line'][$e->severity] }}">{{ $e->severity }}</span></td><td class="font-mono text-[11px]">{{ $e->code }}</td><td>{{ $e->record_ref }}</td><td>{{ $e->field }}</td><td class="text-[12px]">{{ $e->message }}</td></tr>
@empty<tr><td colspan="5"><x-empty title="Tidak ada masalah data" text="Semua record valid dan tidak ada duplikat atau konflik." /></td></tr>@endforelse
</tbody></table></div><div class="p-3 border-t border-line">{{ $issues->links() }}</div></div>
@endsection
