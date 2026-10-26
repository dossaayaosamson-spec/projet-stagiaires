@props([
    'initials',
    'name',
    'bio',
    'count',
    'avatar' => null,
    'color' => '#1B6CA8'
])

<div class="text-center p-[40px_24px] border border-white/10 rounded-[8px] bg-agau-blue-deep transition-all duration-300 cursor-pointer hover:bg-[#2B8ACF]/10 hover:border-[#2B8ACF]/30 hover:-translate-y-[4px]">
    <div class="w-[80px] h-[80px] rounded-full mx-auto mb-[16px] flex items-center justify-center text-[1.5rem] font-black text-white border-[3px] border-[#2B8ACF]/40 overflow-hidden" style="background-color: {{ $color }};">
        @if($avatar)
            <img src="{{ asset('storage/' . $avatar) }}" alt="{{ $name }}" class="w-full h-full object-cover" loading="lazy">
        @else
            {{ $initials }}
        @endif
    </div>
    <h3 class="font-serif text-[1.1rem] text-white font-semibold mb-[6px] m-0">{{ $name }}</h3>
    <p class="text-[0.9rem] text-white/70 leading-[1.7] mb-[16px] line-clamp-3 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical;">{{ $bio }}</p>
    <p class="text-[0.68rem] font-bold tracking-[2px] uppercase text-[#5BC4F5]">{{ $count }} ouvrage{{ $count > 1 ? 's' : '' }} publié{{ $count > 1 ? 's' : '' }}</p>
</div>
