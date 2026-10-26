@props([
    'icon',
    'value',
    'label',
    'sub',
    'route' => null
])

@if($route)
<a href="{{ $route }}" class="block no-underline">
@endif
<div class="bg-white border border-agau-border rounded-[8px] p-[14px_16px] {{ $route ? 'cursor-pointer hover:bg-agau-off-white transition-colors duration-200' : '' }}">
    <div class="text-[16px] mb-[6px]">{{ $icon }}</div>
    <div class="text-[1.4rem] font-black text-agau-text leading-none">{{ $value }}</div>
    <div class="text-[0.6rem] text-agau-muted tracking-[1px] uppercase mt-[3px]">{{ $label }}</div>
    <div class="text-[0.62rem] text-[#1D9E75] mt-[2px] font-semibold">{{ $sub }}</div>
</div>
@if($route)
</a>
@endif
