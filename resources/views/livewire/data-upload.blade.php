<div class="card card-pad" x-data="{ over: false }">
    <h2 class="mb-1">Unggah data sumber</h2>
    <p class="text-[13px] text-ink-500 mb-3">Project Master (CSV), Project Updates (CSV), dan Production Data (JSON). Jenis file dikenali dari struktur, bukan nama file. Maksimal {{ config('ontrack.upload.max_kb') }} KB per file.</p>

    @if (! \App\Support\CurrentUser::get()?->isDirector())
        <x-alert type="warning">Unggah data hanya tersedia untuk Director. Pilih demo user Director pada header.</x-alert>
    @endif

    <label @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop.prevent="over = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change', { bubbles: true }))"
        :class="over ? 'border-accent-600 bg-pend-bg' : 'border-slate-300 bg-canvas'" class="block border-2 border-dashed rounded-lg px-4 py-8 text-center cursor-pointer transition-colors">
        <div class="text-[14px] font-medium">Seret &amp; lepas file di sini</div>
        <div class="text-[12px] text-ink-500">atau klik untuk memilih (bisa lebih dari satu)</div>
        <input x-ref="input" type="file" multiple wire:model="files" accept=".csv,.json,.txt" class="sr-only" aria-label="Pilih file">
    </label>
    <div wire:loading wire:target="files" class="text-[12px] text-ink-500 mt-2">Mengunggah…</div>
    @error('files')<div class="field-error">{{ $message }}</div>@enderror
    @error('files.*')<div class="field-error">{{ $message }}</div>@enderror
    @if ($error)<div class="field-error">{{ $error }}</div>@endif

    @if ($files)
        <ul class="mt-3 divide-y divide-well border border-line rounded">
            @foreach ($files as $i => $f)
                <li class="flex items-center justify-between px-3 py-2 text-[13px]"><span>📄 {{ $f->getClientOriginalName() }} <span class="text-ink-500">· {{ number_format($f->getSize() / 1024, 1) }} KB</span></span>
                    <button type="button" wire:click="removeFile({{ $i }})" class="text-ink-500 hover:text-crit-fg" aria-label="Hapus">✕</button></li>
            @endforeach
        </ul>
    @endif

    <div class="mt-3 flex flex-wrap items-end gap-3">
        <div><label class="label-sm block mb-1" for="asof">Tanggal acuan (as-of)</label><input id="asof" type="datetime-local" wire:model="asOf" class="input !w-56"></div>
        <button type="button" class="btn-accent" wire:click="import" wire:confirm="Proses file yang dipilih? Pipeline akan memvalidasi, menyimpan, dan menghitung ulang temuan serta attention." wire:loading.attr="disabled" wire:target="import" @disabled(! $files)>
            <span wire:loading.remove wire:target="import">Proses data</span><span wire:loading wire:target="import">Memproses…</span></button>
    </div>

    <div class="mt-4 text-[12px] text-ink-500">Alur pemrosesan: Unggah → Validasi → Normalisasi → Simpan → Susun konteks proyek → Olah evidence → Interpretasi → Hitung attention → Verifikasi ulang → Dashboard diperbarui.</div>

    @if ($results)
        <div class="mt-5 space-y-3">
            <h2>Ringkasan proses</h2>
            @foreach ($results as $r)
                <div class="border border-line rounded-lg p-3">
                    <div class="flex items-center justify-between gap-2"><div class="font-medium">{{ $r['file_name'] }} <span class="text-ink-500 font-normal text-[12px]">{{ str_replace('_', ' ', $r['source_kind'] ?? 'tidak dikenali') }}</span></div><x-badge :value="$r['status']" kind="import" /></div>
                    <div class="grid grid-cols-3 md:grid-cols-7 gap-2 mt-2 text-center">
                        @foreach (['total_records' => 'Total', 'valid_records' => 'Valid', 'invalid_records' => 'Invalid', 'missing_field_count' => 'Field kosong', 'duplicate_records' => 'Duplikat', 'conflicting_records' => 'Konflik/revisi', 'new_records' => 'Baru'] as $k => $l)
                            <div class="bg-canvas rounded p-2"><div class="text-[18px] font-bold tabular-nums">{{ $r[$k] }}</div><div class="label-sm">{{ $l }}</div></div>@endforeach
                    </div>
                    <p class="text-[12px] text-ink-700 mt-2">{{ $r['message'] }}</p>
                    <a href="{{ route('data.imports.show', $r['id']) }}" class="text-[12px] text-accent-600 font-medium">Lihat detail &amp; kesalahan validasi →</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
