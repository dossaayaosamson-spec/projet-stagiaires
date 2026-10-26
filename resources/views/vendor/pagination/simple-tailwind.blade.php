@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4 py-4 w-full">
        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-agau-border/20 border border-agau-border/40 text-agau-muted/40 cursor-not-allowed text-xs font-bold uppercase tracking-wider transition-all select-none">
                <i class="bi bi-chevron-left text-xs"></i>
                <span>Précédent</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-agau-border/60 text-agau-muted hover:text-agau-blue hover:border-agau-blue hover:bg-agau-blue/5 transition-all shadow-sm text-xs font-bold uppercase tracking-wider no-underline">
                <i class="bi bi-chevron-left text-xs"></i>
                <span>Précédent</span>
            </a>
        @endif

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-agau-border/60 text-agau-muted hover:text-agau-blue hover:border-agau-blue hover:bg-agau-blue/5 transition-all shadow-sm text-xs font-bold uppercase tracking-wider no-underline">
                <span>Suivant</span>
                <i class="bi bi-chevron-right text-xs"></i>
            </a>
        @else
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-agau-border/20 border border-agau-border/40 text-agau-muted/40 cursor-not-allowed text-xs font-bold uppercase tracking-wider transition-all select-none">
                <span>Suivant</span>
                <i class="bi bi-chevron-right text-xs"></i>
            </span>
        @endif
    </nav>
@endif
