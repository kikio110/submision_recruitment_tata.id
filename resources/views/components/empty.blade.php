@props(['title', 'text' => null, 'href' => null, 'action' => null])
<div class="text-center py-10 px-4">
    <div class="mx-auto w-10 h-10 rounded-full bg-well grid place-items-center text-ink-500 mb-3">∅</div>
    <div class="font-semibold">{{ $title }}</div>
    @if ($text)<p class="text-ink-500 mt-1 text-[13px]">{{ $text }}</p>@endif
    @if ($href)<a href="{{ $href }}" class="btn-accent mt-4">{{ $action }}</a>@endif
</div>
