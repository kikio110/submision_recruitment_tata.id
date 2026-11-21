@props(['title', 'sub' => null])
<div class="flex flex-wrap items-end justify-between gap-3 mb-5">
    <div><h1>{{ $title }}</h1>@if ($sub)<p class="text-ink-500 text-[13px] mt-0.5">{{ $sub }}</p>@endif</div>
    <div class="flex gap-2">{{ $slot }}</div>
</div>
