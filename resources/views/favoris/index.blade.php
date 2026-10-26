@extends('layouts.dashboard')

@section('title', 'Mes Favoris — AGAU Éditions')

@section('content')

<div style="max-width: 1100px; margin: 0 auto; padding: 2rem 1rem;">

    {{-- En-tête --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 600; color: #0B3C5D; margin: 0 0 4px;">
                Mes Favoris
            </h1>
            <p style="color: #666; font-size: 0.95rem; margin: 0;">
                {{ $favoris->count() }} livre{{ $favoris->count() > 1 ? 's' : '' }} sauvegardé{{ $favoris->count() > 1 ? 's' : '' }}
            </p>
        </div>
        <a href="{{ route('front.catalogue') }}"
           style="background: #0B3C5D; color: #fff; padding: 8px 20px; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
            Voir le catalogue
        </a>
    </div>

    {{-- Messages flash --}}
    @if(session('success'))
        <div id="success-alert" style="background: #EAFAF1; border-left: 4px solid #1E8449; padding: 12px 16px; border-radius: 6px; margin-bottom: 1.5rem; color: #1E8449; font-size: 0.9rem; display: flex; justify-content: space-between; align-items: center;">
            <div>{{ session('success') }}</div>
            <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem; line-height: 1; padding: 0;">&times;</button>
        </div>
    @endif

    @if(session('info'))
        <div id="info-alert" style="background: #EBF5FB; border-left: 4px solid #0B3C5D; padding: 12px 16px; border-radius: 6px; margin-bottom: 1.5rem; color: #0B3C5D; font-size: 0.9rem; display: flex; justify-content: space-between; align-items: center;">
            <div>{{ session('info') }}</div>
            <button type="button" onclick="document.getElementById('info-alert').style.display='none'" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem; line-height: 1; padding: 0;">&times;</button>
        </div>
    @endif

    @if($favoris->isEmpty())
        <div style="text-align: center; padding: 4rem 2rem; background: #F5F5F5; border-radius: 12px;">
            <p style="font-size: 2rem; margin-bottom: 1rem;">♡</p>
            <h2 style="color: #0B3C5D; font-size: 1.2rem; margin-bottom: 8px;">Aucun favori pour l'instant</h2>
            <p style="color: #888; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Parcourez le catalogue et ajoutez les livres qui vous intéressent.
            </p>
            <a href="{{ route('front.catalogue') }}"
               style="background: #D4AF37; color: #0B3C5D; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                Explorer le catalogue
            </a>
        </div>

    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">

            @foreach($favoris as $livre)
            <div style="background: #fff; border: 1px solid #eee; border-radius: 10px; overflow: hidden; position: relative;">

                {{-- Bouton retirer --}}
                <form method="POST" action="{{ route('favoris.destroy', $livre->id) }}" style="position: absolute; top: 8px; right: 8px; z-index: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            title="Retirer des favoris"
                            style="background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center;">
                        ♥
                    </button>
                </form>

                {{-- Couverture --}}
                <div style="height: 160px; display: flex; align-items: center; justify-content: center; overflow: hidden;"
                     style="background-color: {{ $livre->couleur_couverture ?? '#0B3C5D' }}">
                    @if($livre->cover_url)
                        <img src="{{ $livre->cover_url }}"
                             alt="{{ $livre->titre }}"
                             style="height: 100%; width: 100%; object-fit: cover;">
                    @else
                        <div style="background: {{ $livre->couleur_couverture ?? '#0B3C5D' }}; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: rgba(255,255,255,0.6); font-size: 0.75rem; text-align: center; padding: 8px;">
                                {{ $livre->titre }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Infos --}}
                <div style="padding: 12px;">
                    <p style="font-size: 0.75rem; color: #D4AF37; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px;">
                        {{ $livre->categorie ?? 'Roman' }}
                    </p>
                    <h3 style="font-size: 0.9rem; font-weight: 600; color: #0B3C5D; margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $livre->titre }}
                    </h3>
                    <p style="font-size: 0.8rem; color: #888; margin: 0 0 10px;">
                        {{ $livre->author->nom ?? '' }}
                    </p>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.9rem; font-weight: 600; color: #0B3C5D;">
                            {{ number_format($livre->prix, 0, ',', ' ') }} FCFA
                        </span>
                        <a href="{{ route('front.livres.show', $livre->frontRouteParams()) }}"
                           style="font-size: 0.75rem; color: #0B3C5D; text-decoration: underline;">
                            Voir
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    @endif

</div>

@endsection
