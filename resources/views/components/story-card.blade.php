@props([
    'id',
    'title',
    'excerpt',
    'author',
    'tags',
    'gradient' => 'from-[#1a2a4a] to-[#2d4a7a]',
    'coverPath' => null
])

<div class="bg-white border border-agau-border rounded-[10px] overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-[0_10px_40px_rgba(15,74,122,0.13)] hover:-translate-y-[5px] flex flex-col">
    <div class="h-[170px] flex items-end p-[20px] relative overflow-hidden bg-gradient-to-br {{ $gradient }}">
        @if($coverPath)
            <img src="{{ asset('storage/' . $coverPath) }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 hover:scale-110" alt="{{ $title }}" loading="lazy">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-[#0A3050]/88 to-transparent via-transparent to-[55%]"></div>
        <span class="font-serif text-[1rem] italic font-semibold text-white relative z-[1] leading-[1.3]">{{ $title }}</span>
    </div>
    <div class="p-[22px] flex flex-col flex-1">
        <p class="text-[0.6rem] font-bold tracking-[2px] uppercase text-agau-blue mb-[10px] flex items-center gap-[6px]">
            <span class="w-[16px] h-[2px] bg-agau-blue inline-block"></span>
            {{ is_array($tags) ? implode(' · ', $tags) : $tags }}
        </p>
        <h3 class="font-serif text-[1.05rem] font-semibold text-agau-text mb-[10px] leading-[1.4] m-0">{{ $title }}</h3>
        <p class="text-[0.95rem] text-agau-muted leading-[1.6] mb-[18px] italic font-serif line-clamp-5 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical;">{{ $excerpt }}</p>
        <div class="flex justify-between items-center pt-[14px] border-t border-agau-border mt-auto">
            <span class="text-[0.8rem] font-bold text-agau-blue tracking-[1px] uppercase">{{ $author }}</span>
            <a href="{{ route('front.stories.show', ['id' => $id, 'slug' => \Illuminate\Support\Str::slug($title)]) }}" class="text-[0.72rem] text-agau-muted cursor-pointer font-semibold tracking-[1px] uppercase transition-colors duration-200 no-underline hover:text-agau-blue" aria-label="Lire {{ $title }}">Lire →</a>
        </div>
    </div>
</div>
