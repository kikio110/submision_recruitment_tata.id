@props(['id', 'title', 'action', 'method' => 'POST', 'confirm' => 'Ya, lanjutkan', 'danger' => false])
{{-- Confirmation dialog (Alpine). Open with: $dispatch('open-modal', '<id>') --}}
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === '{{ $id }}') open = true" x-on:keydown.escape.window="open = false" x-show="open" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4" style="display:none">
    <div class="absolute inset-0 bg-navy-900/50" @click="open = false"></div>
    <form method="POST" action="{{ $action }}" class="relative bg-white rounded-lg border border-line shadow-float w-full max-w-md p-5">
        @csrf @if ($method !== 'POST') @method($method) @endif
        <h2 class="mb-2">{{ $title }}</h2>
        <div class="text-[13px] text-ink-700 mb-4">{{ $slot }}</div>
        <div class="flex justify-end gap-2"><button type="button" class="btn-secondary" @click="open = false">Batal</button>
            <button class="{{ $danger ? 'btn-danger' : 'btn-accent' }}">{{ $confirm }}</button></div>
    </form>
</div>
