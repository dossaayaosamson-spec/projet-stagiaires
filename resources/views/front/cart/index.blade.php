@extends('layouts.app')

@section('title', 'Mon Panier — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
    <div class="max-w-[1000px] mx-auto">
        <div class="flex items-end justify-between mb-[48px]">
            <div>
                <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Votre commande</p>
                <h1 class="font-serif text-[2.4rem] text-agau-text font-semibold m-0">Mon <span class="text-agau-blue">Panier</span></h1>
                <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
            </div>
            <a href="{{ route('front.catalogue') }}" class="text-[0.75rem] font-bold text-agau-muted tracking-[1px] uppercase no-underline hover:text-agau-blue transition-colors">
                Continuer mes achats
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex justify-between items-center" role="alert" id="success-alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').style.display='none'" class="text-green-700 hover:text-green-900 bg-transparent border-none cursor-pointer text-lg font-bold p-0 leading-none">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if(count($cart) > 0)
            {{-- Badge type de panier --}}
            @if($cartType === 'physique')
                <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 text-amber-700 text-[0.75rem] font-bold px-4 py-2 rounded-full mb-6">
                    <i class="bi bi-box-seam"></i> Commande physique — livraison à domicile
                </div>
            @else
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 text-[0.75rem] font-bold px-4 py-2 rounded-full mb-6">
                    <i class="bi bi-download"></i> Commande numérique
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-[32px] items-start">
                <!-- Liste des articles -->
                <div class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-agau-off-white border-b border-agau-border">
                                <th class="px-[24px] py-[16px] text-[0.65rem] font-bold tracking-[1.5px] uppercase text-agau-muted">Livre</th>
                                <th class="px-[24px] py-[16px] text-[0.65rem] font-bold tracking-[1.5px] uppercase text-agau-muted">Prix</th>
                                <th class="px-[24px] py-[16px] text-[0.65rem] font-bold tracking-[1.5px] uppercase text-agau-muted text-center">Quantité</th>
                                <th class="px-[24px] py-[16px] text-[0.65rem] font-bold tracking-[1.5px] uppercase text-agau-muted text-right">Total</th>
                                <th class="px-[24px] py-[16px]"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $id => $details)
                                <tr class="border-b border-agau-border last:border-0 hover:bg-agau-off-white/50 transition-colors">
                                    <td class="px-[24px] py-[24px]">
                                        <div class="flex items-center gap-[16px]">
                                            <img src="{{ $details['cover_url'] }}" alt="{{ $details['title'] }}" class="w-[50px] rounded-[2px] shadow-sm shrink-0" style="object-fit: cover;">
                                            <div>
                                                <div class="font-serif text-[1rem] font-semibold text-agau-text leading-[1.3] mb-[4px]">{{ $details['title'] }}</div>
                                                <div class="text-[0.7rem] font-bold text-agau-blue tracking-[1px] uppercase">{{ $details['author'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-[24px] py-[24px] text-[0.95rem] font-medium text-agau-text">
                                        {{ number_format($details['price'], 0, ',', ' ') }} <span class="text-[0.7rem]">FCFA</span>
                                    </td>
                                    <td class="px-[24px] py-[24px]">
                                        <div class="flex items-center justify-center gap-[12px]">
                                            <input type="number" value="{{ $details['quantity'] }}" class="w-[50px] border border-agau-border rounded-[4px] px-[8px] py-[6px] text-center text-[0.9rem] focus:outline-none focus:border-agau-blue update-cart" data-id="{{ $id }}">
                                        </div>
                                    </td>
                                    <td class="px-[24px] py-[24px] text-right text-[1rem] font-bold text-agau-blue">
                                        {{ number_format($details['price'] * $details['quantity'], 0, ',', ' ') }} <span class="text-[0.7rem] font-medium">FCFA</span>
                                    </td>
                                    <td class="px-[24px] py-[24px] text-right">
                                        <form action="{{ route('cart.remove') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <button type="submit" class="text-agau-muted hover:text-red-500 transition-colors bg-transparent border-0 cursor-pointer p-0 text-[1.2rem]">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Résumé -->
                <div class="bg-white border border-agau-border rounded-[12px] p-[32px] shadow-sm sticky top-[100px]">
                    <h3 class="font-serif text-[1.2rem] font-semibold text-agau-text mb-[24px]">Résumé</h3>
                    <div class="flex justify-between items-center mb-[16px]">
                        <span class="text-agau-muted text-[0.9rem]">Sous-total</span>
                        <span class="text-agau-text font-semibold">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($cartType === 'physique')
                        <div class="flex justify-between items-center mb-[16px]">
                            <span class="text-agau-muted text-[0.9rem]">Frais de livraison</span>
                            <span class="text-amber-600 text-[0.85rem] font-medium">À confirmer (min. 500 FCFA)</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-[32px] pt-[16px] border-t border-agau-border">
                        <span class="text-agau-text font-bold text-[1.1rem]">Total</span>
                        <span class="text-agau-blue font-black text-[1.3rem]">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if($cartType === 'physique')
                        <a href="{{ route('cart.delivery') }}" class="w-full bg-agau-blue text-white border-0 py-[16px] rounded-[4px] text-[0.8rem] font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-colors mb-[16px] text-center block no-underline">
                            Continuer — Adresse de livraison
                        </a>
                    @else
                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-agau-blue text-white border-0 py-[16px] rounded-[4px] text-[0.8rem] font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-colors mb-[16px]">
                                Passer à la caisse
                            </button>
                        </form>
                    @endif

                    <p class="text-[0.7rem] text-center text-agau-muted leading-[1.5]">
                        En cliquant sur commander, vous acceptez nos conditions générales de vente.
                    </p>
                </div>
            </div>
        @else
            <div class="bg-white border border-agau-border rounded-[12px] p-[80px] text-center shadow-sm">
                <div class="w-[80px] h-[80px] bg-agau-off-white rounded-full flex items-center justify-center mx-auto mb-[24px]">
                    <i class="bi bi-cart-x text-agau-blue text-[2.5rem]"></i>
                </div>
                <h2 class="font-serif text-[1.6rem] text-agau-text mb-[12px]">Votre panier est vide</h2>
                <p class="text-agau-muted mb-[32px] max-w-[400px] mx-auto">Il semblerait que vous n'ayez pas encore ajouté de trésors littéraires à votre sélection.</p>
                <a href="{{ route('front.catalogue') }}" class="bg-agau-blue text-white px-[32px] py-[14px] rounded-[4px] text-[0.8rem] font-bold tracking-[2px] uppercase no-underline hover:bg-agau-blue-dark transition-colors inline-block">
                    Découvrir le catalogue
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Logique JS pour mise à jour AJAX si besoin (facultatif ici)
    document.querySelectorAll('.update-cart').forEach(input => {
        input.addEventListener('change', function() {
            let id = this.getAttribute('data-id');
            let quantity = this.value;
            
            fetch("{{ route('cart.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id: id, quantity: quantity })
            }).then(response => {
                window.location.reload();
            });
        });
    });
</script>
@endpush
@endsection
