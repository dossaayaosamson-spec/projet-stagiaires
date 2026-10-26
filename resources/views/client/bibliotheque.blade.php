@extends('layouts.dashboard')

@section('title', 'Ma Bibliothèque — AGAU Éditions')

@section('content')

<div style="max-width: 1100px; margin: 0 auto; padding: 2rem 1rem;">

    {{-- En-tête --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 600; color: #0B3C5D; margin: 0 0 4px;">
                Ma Bibliothèque
            </h1>
            <p style="color: #666; font-size: 0.95rem; margin: 0;">
                {{ $livresAchetes->count() }} livre{{ $livresAchetes->count() > 1 ? 's' : '' }} acheté{{ $livresAchetes->count() > 1 ? 's' : '' }}
            </p>
        </div>
        <a href="{{ route('front.catalogue') }}"
           style="background: #0B3C5D; color: #fff; padding: 8px 20px; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
            Voir le catalogue
        </a>
    </div>

    @if($livresAchetes->isEmpty())
        <div style="text-align: center; padding: 4rem 2rem; background: #F5F5F5; border-radius: 12px;">
            <p style="font-size: 2rem; margin-bottom: 1rem;"> <i class="bi bi-book-open"></i> </p>
            <h2 style="color: #0B3C5D; font-size: 1.2rem; margin-bottom: 8px;">Votre bibliothèque est vide</h2>
            <p style="color: #888; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Achetez des livres pour les retrouver ici et les lire à tout moment.
            </p>
            <a href="{{ route('front.catalogue') }}"
               style="background: #D4AF37; color: #0B3C5D; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                Explorer le catalogue
            </a>
        </div>

    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">

            @foreach($livresAchetes as $livre)
            <div style="background: #fff; border: 1px solid #eee; border-radius: 10px; overflow: hidden; position: relative; transition: box-shadow 0.2s;" 
                 onmouseover="this.style.boxShadow='0 4px 20px rgba(11,60,93,0.12)'" 
                 onmouseout="this.style.boxShadow='none'">

                {{-- Badge Acheté --}}
                <div style="position: absolute; top: 8px; left: 8px; z-index: 1; background: #1E8449; color: white; font-size: 0.6rem; font-weight: 700; padding: 3px 8px; border-radius: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                    ✓ Acheté
                </div>

                {{-- Couverture --}}
                <div style="height: 180px; overflow: hidden; position: relative;">
                    @if($livre->cover_url)
                        <img src="{{ $livre->cover_url }}"
                             alt="{{ $livre->titre }}"
                             style="height: 100%; width: 100%; object-fit: cover;">
                    @else
                        <div style="background: {{ $livre->couleur_couverture ?? '#0B3C5D' }}; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.8rem; text-align: center; padding: 12px; font-style: italic;">
                                {{ $livre->titre }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Infos --}}
                <div style="padding: 12px;">
                    <p style="font-size: 0.7rem; color: #D4AF37; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px;">
                        {{ $livre->categorie ?? 'Roman' }}
                    </p>
                    <h3 style="font-size: 0.9rem; font-weight: 600; color: #0B3C5D; margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                        title="{{ $livre->titre }}">
                        {{ $livre->titre }}
                    </h3>
                    <p style="font-size: 0.78rem; color: #888; margin: 0 0 12px;">
                        {{ $livre->author->nom ?? '' }}
                    </p>
                    <a href="{{ route('read.book', $livre->id) }}"
                       style="display: block; text-align: center; background: #0B3C5D; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 0.78rem; font-weight: 600; transition: background 0.2s;"
                       onmouseover="this.style.background='#1B6CA8'"
                       onmouseout="this.style.background='#0B3C5D'">
                        📖 Lire
                    </a>
                </div>
            </div>
            @endforeach

        </div>
    @endif

</div>

@endsection
