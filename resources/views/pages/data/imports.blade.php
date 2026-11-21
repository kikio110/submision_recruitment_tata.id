@extends('layouts.app')
@section('crumb', 'Data Management / Import History')
@section('content')
<x-page-title title="Import History" sub="Setiap file yang diproses, termasuk yang ditolak atau gagal."><a href="{{ route('data.index') }}" class="btn-secondary">Kembali</a></x-page-title>
<div class="card"><div class="overflow-x-auto"><table class="tbl"><thead><tr><th>#</th><th>File</th><th>Jenis</th><th>Waktu</th><th>As-of</th><th>Total</th><th>Valid</th><th>Baru</th><th>Duplikat</th><th>Konflik</th><th>Ditolak</th><th>Status</th></tr></thead><tbody>
@forelse ($batches as $b)
<tr class="cursor-pointer" onclick="window.location='{{ route('data.imports.show', $b) }}'"><td>{{ $b->id }}</td><td class="font-medium"><a class="text-accent-600" href="{{ route('data.imports.show', $b) }}">{{ $b->file_name }}</a></td><td class="text-[12px]">{{ str_replace('_', ' ', $b->source_kind ?? '—') }}</td><td class="whitespace-nowrap">{{ $b->created_at->format('d M Y H:i') }}</td><td class="whitespace-nowrap">{{ $b->as_of->format('d M Y') }}</td>
<td class="tabular-nums">{{ $b->total_records }}</td><td class="tabular-nums">{{ $b->valid_records }}</td><td class="tabular-nums">{{ $b->new_records }}</td><td class="tabular-nums">{{ $b->duplicate_records }}</td><td class="tabular-nums">{{ $b->conflicting_records }}</td><td class="tabular-nums">{{ $b->invalid_records }}</td><td><x-badge :value="$b->status" kind="import" /></td></tr>
@empty<tr><td colspan="12"><x-empty title="Belum ada import" :href="route('data.index')" action="Unggah data" /></td></tr>@endforelse
</tbody></table></div><div class="p-3 border-t border-line">{{ $batches->links() }}</div></div>
@endsection
