@props(['book', 'userRating' => null])

@php
    $hasRatingSummary = $book->getAttribute('ratings_count') !== null;
    $ratingsCount = (int) ($book->getAttribute('ratings_count') ?? 0);
    $averageRating = (float) ($book->getAttribute('ratings_avg_rating') ?? 0);
@endphp

@if($hasRatingSummary)
    <div class="mt-0 mb-[4px]">
        @auth
            <form method="POST" action="{{ route($userRating ? 'front.ratings.update' : 'front.ratings.store', $book->id) }}" class="inline-flex gap-[3px]" aria-label="Noter {{ $book->titre }} sur 5">
                @csrf
                @if($userRating)
                    @method('PUT')
                @endif
                <span class="inline-flex gap-[3px] text-amber-500" style="font-size: 40px; line-height: 1" aria-hidden="true">
                    @for($star = 1; $star <= 5; $star++)
                        <button type="submit" name="rating" value="{{ $star }}" class="border-0 bg-transparent p-0 text-amber-500 cursor-pointer leading-none hover:scale-110 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2" title="Noter {{ $star }} sur 5" aria-label="Noter {{ $book->titre }} {{ $star }} sur 5">
                            @if($ratingsCount > 0 && $star <= round($averageRating))&#9733;@else&#9734;@endif
                        </button>
                    @endfor
                </span>
            </form>
        @else
            <details>
                <summary class="w-fit cursor-pointer list-none" style="list-style: none" aria-label="Ouvrir les options pour noter {{ $book->titre }}">
                    <span class="inline-flex gap-[3px] text-amber-500" style="font-size: 40px; line-height: 1" aria-hidden="true">
                        @for($star = 1; $star <= 5; $star++)
                            <span>@if($ratingsCount > 0 && $star <= round($averageRating))&#9733;@else&#9734;@endif</span>
                        @endfor
                    </span>
                </summary>
                <div class="mt-[6px] text-[0.8rem]">
                    <p class="mb-[4px] text-agau-muted">Veuillez vous connecter pour pouvoir noter ce livre.</p>
                    <a href="{{ route('login') }}" class="font-bold text-agau-blue underline">Se connecter</a>
                </div>
            </details>
        @endauth
        @if($ratingsCount > 0)
            <p class="mt-[4px] text-[0.8rem] text-agau-text font-semibold">
                {{ number_format($averageRating, 1, ',', ' ') }}/5 · {{ $ratingsCount }} {{ $ratingsCount === 1 ? 'note' : 'notes' }}
            </p>
        @endif
    </div>
@endif