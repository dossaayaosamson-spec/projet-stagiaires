@props([
    'id' => null,
    'title' => null,
    'author' => null,
    'badge' => null,
    'color' => '#1B6CA8',
    'cover' => null,
    'book' => null
])

@php
    if ($book) {
        $id = $id ?? $book->id;
        $title = $title ?? $book->titre;
        $author = $author ?? ($book->author->nom ?? 'Inconnu');
        $badge = $badge ?? $book->badge;
        $color = $color ?? ($book->couleur_couverture ?? '#1B6CA8');
        $cover = $cover ?? $book->cover_url;
    }
    $bookUrl = $book ? route('front.livres.show', $book->frontRouteParams()) : ($id ? route('front.livres.show', $id) : null);
@endphp

<div class="cursor-pointer transition-transform duration-300 hover:-translate-y-[10px] group">
    <div class="aspect-[2/3] rounded-[4px_10px_10px_4px] mb-[16px] relative overflow-hidden shadow-[0_8px_24px_rgba(15,74,122,0.18)] flex items-end p-[16px] transition-shadow group-hover:shadow-[0_12px_40px_rgba(15,74,122,0.25)]" @style(['background-color: ' . $color => true])>
        @if($bookUrl)
            <a href="{{ $bookUrl }}" class="absolute inset-0 z-[1]" aria-label="Voir {{ $title }}">
                @if($cover)
                    <img src="{{ $cover }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" alt="{{ $title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0A3050]/90 to-transparent via-transparent to-[60%]"></div>
                @else
                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-agau-blue/10 to-agau-teal/10">
                        <i class="bi bi-book text-agau-blue/20 text-[80px]"></i>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0A3050]/90 to-transparent via-transparent to-[60%]"></div>
                @endif
                <div class="absolute bottom-[16px] left-[16px] right-[16px] font-serif text-[0.8rem] text-white italic leading-[1.3] truncate">{{ $title }}</div>
            </a>
        @elseif($cover)
            <img src="{{ $cover }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" alt="{{ $title }}">
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-agau-blue/10 to-agau-teal/10">
                <i class="bi bi-book text-agau-blue/20 text-[80px]"></i>
            </div>
        @endif
        @auth
        <form method="POST" action="{{ route('favoris.toggle') }}" class="absolute top-[8px] right-[8px] z-10 favoris-form">
            @csrf
            <input type="hidden" name="livre_id" value="{{ $id }}">
            <button type="submit" class="bg-white/20 hover:bg-white/40 text-white border-none rounded-full w-[28px] h-[28px] flex items-center justify-center text-[14px] cursor-pointer backdrop-blur-sm">
                @if(auth()->user()->aEnFavori($id))
                    <i class="bi bi-heart-fill"></i>
                @else
                    <i class="bi bi-heart"></i>
                @endif
            </button>
        </form>
        @endauth
    </div>
    <h3 class="text-[1.5rem] font-bold text-agau-text m-0 leading-[1.3] group-hover:text-agau-blue transition-colors">
        @if($bookUrl)<a href="{{ $bookUrl }}" class="text-inherit no-underline">{{ $title }}</a>@else{{ $title }}@endif
    </h3>
    @if($book)
        <x-book-rating :book="$book" :user-rating="$userRating ?? null" />
    @endif
    <p class="text-[1rem] text-agau-muted m-0">{{ $author }}</p>
    @if($badge)
        <span class="inline-block mt-[8px] text-[0.6rem] font-black tracking-[2px] uppercase text-agau-blue bg-agau-blue/5 border border-agau-blue/10 px-[10px] py-[3px] rounded-[3px]">{{ $badge }}</span>
    @endif
</div>
