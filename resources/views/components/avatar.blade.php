@props(['src' => null, 'name', 'size' => '10'])

@php
    $colors = ['#f87171', '#fb923c', '#fbbf24', '#a3e635', '#4ade80', '#34d399', '#2dd4bf', '#60a5fa', '#818cf8', '#a78bfa'];
    $colorIndex = abs(crc32($name)) % count($colors);
    $bgColor = $colors[$colorIndex];
@endphp

<div class="flex-shrink-0 h-{{ $size }} w-{{ $size }} rounded-full overflow-hidden">
    @if ($src)
        <img class="w-full h-full object-cover" src="{{ $src }}" alt="{{ $name }}">
    @else
        <div class="w-full h-full flex items-center justify-center text-white font-bold" style="background-color: {{ $bgColor }};">
            <span class="text-lg">{{ getInitials($name) }}</span>
        </div>
    @endif
</div>