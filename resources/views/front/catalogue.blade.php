@extends('layouts.app')

@section('title', 'Catalogue — AGAU Éditions')

@section('content')
<style>
    .page-header {
      background: linear-gradient(135deg, #1B6CA8 0%, #0F4A7A 100%);
      color: white;
      padding: 4rem 1rem;
      text-align: center;
    }
    
    .page-header h1 {
      font-size: 2.8rem;
      margin-bottom: 1rem;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      letter-spacing: -0.5px;
    }
    
    .page-header p {
      font-size: 1.125rem;
      opacity: 0.9;
      max-width: 42rem;
      margin: 0 auto;
      font-weight: 400;
    }
    
    .filters-container {
      background: white;
      padding: 1.5rem;
      margin: -2rem auto 3rem;
      border-radius: 12px;
      max-width: 1000px;
      box-shadow: 0 10px 30px rgba(15, 74, 122, 0.1);
      position: relative;
      z-index: 10;
      border: 1px solid #f0f4f8;
    }
    
    .search-input {
      padding: 14px 20px 14px 50px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      width: 100%;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background-color: #f8fafc;
    }

    .search-input:focus {
        background-color: white;
        border-color: #1B6CA8;
        box-shadow: 0 0 0 4px rgba(27, 108, 168, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 32px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 20px;
    }

    /* Card customization to match the snippet style */
    .book-card {
        background: white;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .book-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(15, 74, 122, 0.1);
        border-color: #1B6CA8/30;
    }

    .card-img-container {
        aspect-ratio: 2/3;
        position: relative;
        overflow: hidden;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(27, 168, 117, 0.9);
        color: white;
        backdrop-filter: blur(4px);
    }
</style>

<div class="bg-agau-off-white min-h-screen pb-[100px]">
    <!-- Page Header -->
    <div class="page-header">
        <div class="absolute w-96 h-96 rounded-full opacity-10" style="background:#5BC4F5;"></div>
        <p class="text-[0.7rem] font-bold tracking-[3.5px] uppercase mb-[12px] opacity-80">Notre Collection Exclusive</p>
        <h2 class="font-lora text-4xl md:text-5xl font-bold text-white mb-2">Catalogue complet</h2>
        <p>Découvrez tous nos livres disponibles, des essais économiques aux romans d'aventure.</p>
    </div>

    <div class="px-[5%] md:px-[8%]">
        <!-- Filter Bar -->
        <div class="filters-container">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-agau-muted/60 text-[14px]"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par titre, auteur ou description...">
            </div>
            
            <!-- Category Shortcuts -->
            <div class="flex flex-wrap gap-[10px] mt-[20px] justify-center">
                <a href="{{ route('front.catalogue') }}" class="px-[16px] py-[8px] text-[0.7rem] font-bold tracking-[0.5px] uppercase rounded-[6px] no-underline transition-all duration-200 {{ !request('filter') ? 'bg-agau-blue text-white shadow-md' : 'bg-white text-agau-text border border-agau-border hover:border-agau-blue hover:text-agau-blue' }}">
                    Tous les livres
                </a>
                <a href="{{ route('front.catalogue', ['filter' => 'bestseller']) }}" class="px-[16px] py-[8px] text-[0.7rem] font-bold tracking-[0.5px] uppercase rounded-[6px] no-underline transition-all duration-200 {{ request('filter') == 'bestseller' ? 'bg-agau-blue text-white shadow-md' : 'bg-white text-agau-text border border-agau-border hover:border-agau-blue hover:text-agau-blue' }}">
                    Best-Sellers
                </a>
                <a href="{{ route('front.stories.index') }}" class="px-[16px] py-[8px] text-[0.7rem] font-bold tracking-[0.5px] uppercase rounded-[6px] no-underline bg-white text-agau-text border border-agau-border hover:border-agau-blue hover:text-agau-blue">
                    Short Stories
                </a>
            </div>
        </div>

        @if($books->isEmpty())
            <div class="bg-white border border-agau-border rounded-[12px] p-[80px] text-center shadow-sm max-w-[800px] mx-auto">
                <h3 class="font-serif text-[1.8rem] text-agau-text mb-[12px]">Catalogue en cours de remplissage</h3>
                <p class="text-[0.95rem] text-agau-muted mb-[32px] max-w-[500px] mx-auto">Notre bibliothèque s'agrandit chaque jour. Revenez très bientôt pour découvrir nos nouveautés !</p>
                <a href="{{ route('home') }}" class="bg-agau-blue text-white px-[32px] py-[14px] rounded-[4px] text-[0.75rem] font-bold tracking-[1.5px] uppercase no-underline hover:bg-agau-blue-dark transition-all inline-block hover:-translate-y-[2px]">Retour à l'accueil</a>
            </div>
        @else
            <div id="booksGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[20px] lg:gap-[32px]">
                @foreach($books as $book)
                    <div class="book-card-item" data-title="{{ Str::lower($book->titre) }}" data-author="{{ Str::lower($book->author->nom ?? 'inconnu') }}" data-desc="{{ Str::lower(Str::limit($book->resume, 100)) }}">
                        <div class="book-card">
                            <div class="card-img-container shadow-inner">
                                @if($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->titre }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full" style="background: {{ $book->couleur_couverture ?? 'linear-gradient(135deg, #1B6CA8 0%, #0F4A7A 100%)' }}; display: flex; align-items: center; justify-content: center; color: white; font-size: 40px;">
                                        <i class="bi bi-journal-bookmark-fill"></i>
                                    </div>
                                @endif
                                
                                @if($book->badge)
                                    <div class="card-badge">{{ $book->badge }}</div>
                                @endif
                                
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            
                            <div class="p-[20px] flex flex-col flex-1">
                                <h3 class="text-[1rem] font-bold text-agau-text leading-[1.3] m-0 mb-[1px] line-clamp-2 min-h-[2.6em]">{{ $book->titre }}</h3>

                                <x-book-rating :book="$book" :user-rating="$userRatings->get($book->id)" />
                                <p class="text-[0.75rem] font-medium text-agau-muted mb-[12px] m-0">{{ $book->author->nom ?? 'Inconnu' }}</p>
                                
                                <p class="text-[0.8rem] text-agau-muted mb-[24px] line-clamp-2 flex-1">{{ $book->resume }}</p>
                                
                                <div class="mt-auto flex items-center justify-between gap-[10px]">
                                    <div class="text-[0.95rem] font-serif font-bold text-agau-blue">
                                        {{ number_format($book->prix, 0, ',', ' ') }} <span class="text-[0.7em] font-sans">FCFA</span>
                                    </div>
                                    <a href="{{ route('front.livres.show', $book->frontRouteParams()) }}" class="bg-agau-off-white text-agau-blue border border-agau-blue/20 px-4 py-2 rounded-[6px] text-[0.75rem] font-bold uppercase tracking-[0.5px] no-underline hover:bg-agau-blue hover:text-white transition-all">
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty result message for search -->
            <div id="noResults" class="hidden bg-white border border-agau-border rounded-[12px] p-[60px] text-center shadow-sm max-w-[600px] mx-auto mt-[40px]">
                <div class="text-[40px] mb-[15px] opacity-40 text-agau-blue"><i class="bi bi-search"></i></div>
                <h3 class="text-[1.2rem] text-agau-text font-bold mb-[8px]">Aucun résultat correspondant</h3>
                <p class="text-[0.9rem] text-agau-muted">Essayez avec d'autres mots-clés (titre, auteur...)</p>
            </div>

            <div class="mt-[60px] flex justify-center pagination-container">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const bookItems = document.querySelectorAll('.book-card-item');
        const noResults = document.getElementById('noResults');
        const pagination = document.querySelector('.pagination-container');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                let visibleCount = 0;

                bookItems.forEach(item => {
                    const title = item.getAttribute('data-title');
                    const author = item.getAttribute('data-author');
                    const desc = item.getAttribute('data-desc');

                    if (title.includes(term) || author.includes(term) || desc.includes(term)) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Gérer l'affichage du message "Aucun résultat"
                if (visibleCount === 0 && term !== "") {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }

                // Cacher la pagination si on recherche (car le filtrage JS ne gère pas les autres pages)
                if (term !== "") {
                    if (pagination) pagination.style.display = 'none';
                } else {
                    if (pagination) pagination.style.display = 'flex';
                }
            });
        }
    });
</script>
@endpush
@endsection
