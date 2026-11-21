@props(['type' => 'info'])
@php $c = ['success' => 'bg-good-bg border-good-bd text-good-fg', 'error' => 'bg-crit-bg border-crit-bd text-crit-fg', 'warning' => 'bg-warn-bg border-warn-bd text-warn-fg', 'info' => 'bg-pend-bg border-pend-bd text-pend-fg'][$type]; @endphp
<div x-data="{ open: true }" x-show="open" role="alert" class="mb-4 flex items-start justify-between gap-3 border rounded px-3 py-2 text-[13px] {{ $c }}">
    <div>{{ $slot }}</div><button type="button" @click="open = false" class="opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
</div>
