@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-4 w-full">
        {{-- Compteur d'éléments en français soigné --}}
        <div class="text-xs text-agau-muted text-center sm:text-left font-medium">
            @if ($paginator->firstItem())
                Affichage de <span class="font-bold text-agau-text">{{ $paginator->firstItem() }}</span> à <span class="font-bold text-agau-text">{{ $paginator->lastItem() }}</span> sur <span class="font-bold text-agau-text">{{ $paginator->total() }}</span> résultats
            @else
                <span class="font-bold text-agau-text">{{ $paginator->count() }}</span> résultat(s)
            @endif
        </div>

        {{-- Liens de navigation aérés --}}
        <div class="flex items-center justify-center gap-1.5 flex-wrap">
            {{-- Lien Précédent --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-agau-border/20 border border-agau-border/40 text-agau-muted/40 cursor-not-allowed text-xs transition-all select-none">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-agau-border/60 text-agau-muted hover:text-agau-blue hover:border-agau-blue hover:bg-agau-blue/5 transition-all shadow-sm text-xs no-underline">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            {{-- Éléments de pagination --}}
            @foreach ($elements as $element)
                {{-- Séparateur "..." --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex items-center justify-center w-8 h-9 text-xs text-agau-muted font-bold tracking-widest select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Numéros de page --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-agau-blue text-white font-bold text-xs shadow-md shadow-agau-blue/25 transition-all select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-agau-border/60 text-agau-text hover:text-agau-blue hover:border-agau-blue hover:bg-agau-blue/5 font-semibold text-xs transition-all shadow-sm no-underline" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Lien Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-agau-border/60 text-agau-muted hover:text-agau-blue hover:border-agau-blue hover:bg-agau-blue/5 transition-all shadow-sm text-xs no-underline">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-agau-border/20 border border-agau-border/40 text-agau-muted/40 cursor-not-allowed text-xs transition-all select-none">
                    <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
