
@auth
    @php
        $estFavori = auth()->user()->aEnFavori($livre->id);
    @endphp

    <form method="POST" action="{{ route('favoris.toggle') }}" style="display: inline;">
        @csrf
        <input type="hidden" name="livre_id" value="{{ $livre->id }}">
        <button type="submit"
                title="{{ $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                style="
                    background: {{ $estFavori ? '#D4AF37' : 'transparent' }};
                    border: 1.5px solid {{ $estFavori ? '#D4AF37' : '#ccc' }};
                    border-radius: 8px;
                    padding: 6px 14px;
                    cursor: pointer;
                    font-size: 0.85rem;
                    color: {{ $estFavori ? '#0B3C5D' : '#888' }};
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                ">
            {{ $estFavori ? '♥ Favori' : '♡ Ajouter aux favoris' }}
        </button>
    </form>

@else
    <a href="{{ route('login') }}"
       style="
           background: transparent;
           border: 1.5px solid #ccc;
           border-radius: 8px;
           padding: 6px 14px;
           text-decoration: none;
           font-size: 0.85rem;
           color: #888;
           display: inline-flex;
           align-items: center;
           gap: 6px;
       ">
        ♡ Ajouter aux favoris
    </a>
@endauth
