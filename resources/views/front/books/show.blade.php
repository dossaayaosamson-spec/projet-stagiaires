@extends('layouts.app')

@section('title', $book->titre . ' — AGAU Éditions')

@section('content')
<style>
    .details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px 80px;
    }
    
    .details-header {
        color: #1B6CA8;
        padding: 40px;
        border-radius: 12px;
        margin-bottom: 32px;
        box-shadow: 0 10px 30px rgba(15, 74, 122, 0.15);
    }
    
    .breadcrumb {
        font-size: 0.8rem;
        margin-bottom: 24px;
        opacity: 0.8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .breadcrumb a {
        color: #1B6CA8;
        text-decoration: none;
        border-bottom: 1px solid rgba(255,255,255,0.3);
        transition: all 0.2s;
    }
    
    .breadcrumb a:hover {
        border-bottom-color: #1B6CA8;
        opacity: 1;
    }
    
    .details-content {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 48px;
        align-items: start;
    }
    
    .book-cover-card {
        padding: 24px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 8px 40px rgba(0,0,0,0.08);
    }
    
    .book-cover-wrapper {
        width: 100%;
        aspect-ratio: 2/3;
        background: linear-gradient(135deg, #1B6CA8, #0F4A7A);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1B6CA8;
        font-size: 4rem;
        margin: 0 auto;
        overflow: hidden;
        position: relative;
        box-shadow: -10px 15px 35px rgba(0,0,0,0.2);
    }

    .book-cover-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .book-cover-wrapper::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 12px;
        z-index: 2;
    }
    
    .book-info-panel {
        display: flex;
        flex-direction: column;
    }
    
    .book-title-main {
        font-family: 'Lora', serif;
        font-size: 2.4rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #1B6CA8;
        line-height: 1.2;
    }
    
    .book-author-sub {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 32px;
    }

    .info-box {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        padding: 16px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .info-label {
        font-weight: 700;
        color: #1B6CA8;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }
    
    .info-value {
        color: #1B6CA8;
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .description-card {
        padding: 40px;
        border-radius: 12px;
        line-height: 1.8;
        margin-bottom: 32px;
        border: 1px solid #f0f4f8;
        box-shadow: 0 4px 20px rgba(15, 74, 122, 0.03);
    }
    
    .description-title {
        font-size: 1.2rem;
        color: #0F4A7A;
        margin-bottom: 20px;
        font-weight: 700;
        border-bottom: 3px solid #1B6CA8;
        padding-bottom: 8px;
        display: inline-block;
    }

    .description-text {
        font-family: 'Lora', serif;
        font-size: 1rem;
        color: #4a5568;
        font-style: italic;
    }
    
    .action-row {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }
    
    .action-btn {
        flex: 1;
        min-width: 140px;
        padding: 14px 16px;
        border: none;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-buy {
        background: white;
        color: #1B6CA8;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .btn-buy:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn-read {
        background: #D4AF37;
        color: #0B3C5D;
        box-shadow: 0 4px 15px rgba(212,175,55,0.3);
    }

    .btn-read:hover {
        background: #c9a42e;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212,175,55,0.4);
    }
    
    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #1B6CA8;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    .btn-favori {
        background: transparent;
        color: #1B6CA8;
        border: 1.5px solid #1B6CA8;
    }

    .btn-favori.active {
        background: #D4AF37;
        color: #0B3C5D;
        border-color: #D4AF37;
    }
    
    .status-alert {
        background: #f0fff4;
        color: #22543d;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        border: 1px solid #c6f6d5;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-alert.error {
        background: #fff5f5;
        color: #c53030;
        border-color: #fed7d7;
    }

    @media (max-width: 768px) {
        .details-content {
            grid-template-columns: 1fr;
        }
        
        .book-title-main {
            font-size: 1.8rem;
        }
        
        .action-row {
            flex-direction: column;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="bg-agau-off-white min-h-screen">
    <div class="details-container">
        <!-- Header Panel -->
        <div class="details-header">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a> / 
                <a href="{{ route('front.catalogue') }}">Catalogue</a> / 
                <span>{{ $book->titre }}</span>
            </div>
            
            @if(session('success'))
                <div class="status-alert" id="success-alert" style="display: flex; justify-content: space-between;">
                    <div><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
                    <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem; line-height: 1; padding: 0;">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="status-alert error" id="error-alert" style="display: flex; justify-content: space-between;">
                    <div><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
                    <button type="button" onclick="document.getElementById('error-alert').style.display='none'" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem; line-height: 1; padding: 0;">&times;</button>
                </div>
            @endif
            
            <div class="details-content">
                <div class="book-cover-card">
                    <div class="book-cover-wrapper" style="position: relative;">
                        @auth
                        <form method="POST" action="{{ route('favoris.toggle') }}" class="absolute top-[12px] right-[12px] z-10 favoris-form">
                            @csrf
                            <input type="hidden" name="livre_id" value="{{ $book->id }}">
                            <button type="submit" class="bg-black/30 hover:bg-black/50 text-white border-none rounded-full w-[48px] h-[48px] flex items-center justify-center text-[18px] cursor-pointer backdrop-blur-md shadow-lg transition-colors">
                                @if(auth()->user()->aEnFavori($book->id))
                                    <i class="bi bi-heart-fill text-red-500"></i>
                                @else
                                    <i class="bi bi-heart"></i>
                                @endif
                            </button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="absolute top-[12px] right-[12px] z-10 favoris-form">
                            <button type="button" class="bg-black/30 hover:bg-black/50 text-white border-none rounded-full w-[36px] h-[36px] flex items-center justify-center text-[18px] cursor-pointer backdrop-blur-md shadow-lg transition-colors">
                                <i class="bi bi-heart"></i>
                            </button>
                        </a>
                        @endauth

                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->titre }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center italic opacity-30 text-white"><i class="bi bi-book text-6xl"></i></div>
                        @endif
                    </div>
                </div>
                
                <div class="book-info-panel">
                    <h1 class="book-title-main">{{ $book->titre }}</h1>
                    <p class="book-author-sub">par <a href="{{ route('front.auteurs.show', $book->author?->frontRouteParams() ?? ['id' => $book->author_id, 'slug' => 'auteur']) }}" style="color: #1B6CA8; text-decoration: underline;">{{ $book->author->nom ?? 'Auteur inconnu' }}</a></p>
                    
                    <div class="info-grid">
                        <div class="info-box">
                            <div class="info-label">Prix unitaire</div>
                            <div class="info-value text-lg">{{ number_format($book->prix, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                        </div>
                        
                        <div class="info-box">
                            <div class="info-label">Catégorie</div>
                            <div class="info-value">{{ $book->categorie ?? 'Non classé' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Format</div>
                            <div class="info-value">{{ $book->format }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Disponibilité</div>
                            <div class="info-value">{{ $book->disponibilite }}</div>
                        </div>

                        @if($book->maison_edition)
                        <div class="info-box">
                            <div class="info-label">Éditeur</div>
                            <div class="info-value">{{ $book->maison_edition }}</div>
                        </div>
                        @endif

                        @if($book->is_free && ($book->free_until === null || $book->free_until->isFuture()))
                        <div class="info-box" style="background: #EAFAF1; border-color: #1E8449;">
                            <div class="info-label" style="color: #1E8449;">Accès</div>
                            <div class="info-value" style="color: #1E8449;">
                                <i class="bi bi-book-half"></i> Gratuit
                                @if($book->free_until)
                                    jusqu'au {{ $book->free_until->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>

                    @php
                        /** @var \App\Models\Book $book */
                        $estGratuit = $book->is_free && ($book->free_until === null || $book->free_until->isFuture());
                        $peutLire = $estGratuit;
                        if (auth()->check() && !$peutLire) {
                            $peutLire = auth()->user()->activeSubscription !== null
                                || auth()->user()->orders()
                                    ->where('statut', 'paye')
                                    ->whereHas('items', fn($q) => $q->where('book_id', $book->id))
                                    ->exists();
                        }
                    @endphp

                    <div class="action-row">

                        @if($peutLire)
                            <a href="{{ route('read.book', $book->id) }}" class="action-btn btn-read">
                                 Lire maintenant
                            </a>
                        @endif

                        @if($book->disponibilite === 'Les deux')
                            <form action="{{ route('cart.add', $book->id) }}" method="POST" style="flex: 1; min-width: 140px;">
                                @csrf
                                <input type="hidden" name="type" value="physique">
                                <button type="submit" class="action-btn btn-buy w-full" style="border: 2px solid #D4AF37; background: #FFFDF0; color: #8A6D0B;">
                                    <i class="bi bi-box-seam text-lg"></i> Panier Physique
                                </button>
                            </form>
                            <form action="{{ route('cart.add', $book->id) }}" method="POST" style="flex: 1; min-width: 140px;">
                                @csrf
                                <input type="hidden" name="type" value="numerique">
                                <button type="submit" class="action-btn btn-buy w-full">
                                    <i class="bi bi-file-earmark-pdf text-lg"></i> Panier Numérique
                                </button>
                            </form>
                        @elseif($book->isPhysique())
                            <form action="{{ route('cart.add', $book->id) }}" method="POST" style="flex: 1; min-width: 140px;">
                                @csrf
                                <input type="hidden" name="type" value="physique">
                                <button type="submit" class="action-btn btn-buy w-full">
                                    <i class="bi bi-box-seam text-lg"></i> Commander Livre Physique
                                </button>
                            </form>
                        @else
                            <form action="{{ route('cart.add', $book->id) }}" method="POST" style="flex: 1; min-width: 140px;">
                                @csrf
                                <input type="hidden" name="type" value="numerique">
                                <button type="submit" class="action-btn btn-buy w-full">
                                    <i class="bi bi-cart3 text-lg"></i> Acheter Livre Numérique
                                </button>
                            </form>
                        @endif



                        <a href="{{ route('front.catalogue') }}" class="action-btn btn-back">
                            <i class="bi bi-arrow-left text-xl"></i> Catalogue
                        </a>
                    </div>
                    @guest
                        @if(!$estGratuit)
                            <p style="font-size: 0.8rem; color: #888; margin-top: 12px; text-align: center;">
                                <a href="{{ route('login') }}" style="color: #1B6CA8; font-weight: 600;">Connectez-vous</a> pour accéder à la lecture si vous avez acheté ce livre.
                            </p>
                        @endif
                    @endguest

                </div>
            </div>
        </div>
        
        <div class="description-card">
            <div class="description-title">Résumé de l'ouvrage</div>
            <div class="description-text whitespace-pre-line">
                {{ $book->resume ?? "Aucun résumé n'est disponible pour le moment." }}
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8 pt-8 border-t border-gray-100">
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <span class="w-8 h-8 rounded-full bg-agau-blue/5 flex items-center justify-center text-agau-blue text-xs font-bold">✓</span>
                    Lecture sécurisée en ligne
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <span class="w-8 h-8 rounded-full bg-agau-blue/5 flex items-center justify-center text-agau-blue text-xs font-bold">✓</span>
                    Lecture illimitée à vie après achat
                </div>
            </div>
        </div>

        @include('components.comments-section', ['model' => $book])
    </div>
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById('reply-form-' + id);
    if (form) { form.style.display = form.style.display === 'none' ? 'block' : 'none'; }
}
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    if (form) { form.style.display = form.style.display === 'none' ? 'block' : 'none'; }
}
function showArchiveModal(id) {
    const modal = document.getElementById('archive-modal-' + id);
    if (modal) { modal.style.display = 'flex'; }
}
function closeArchiveModal(id) {
    const modal = document.getElementById('archive-modal-' + id);
    if (modal) { modal.style.display = 'none'; }
}
</script>
@endsection