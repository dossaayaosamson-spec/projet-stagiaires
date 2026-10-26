@extends('layouts.app')

@section('title', 'Confirmer votre commande — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
    <div class="max-w-[1000px] mx-auto">
        <div class="flex items-end justify-between mb-[48px]">
            <div>
                <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Étape 2/2</p>
                <h1 class="font-serif text-[2.4rem] text-agau-text font-semibold m-0">Résumé de votre <span class="text-agau-blue">Commande</span></h1>
                <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
            </div>
            <a href="{{ route('cart.index') }}" class="text-[0.75rem] font-bold text-agau-muted tracking-[1px] uppercase no-underline hover:text-agau-blue transition-colors">
                Retour panier
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-[32px] items-start">
            <!-- Résumé des articles -->
            <div class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-sm">
                <div class="bg-agau-off-white border-b border-agau-border px-[24px] py-[16px]">
                    <h2 class="text-[0.85rem] font-bold text-agau-text uppercase tracking-[1px] m-0">Résumé de votre panier ({{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }})</h2>
                </div>
                
                <div class="divide-y divide-agau-border">
                    @foreach($cart as $id => $details)
                    <div class="px-[24px] py-[20px] hover:bg-agau-off-white/30 transition-colors">
                        <div class="flex items-start gap-[16px]">
                            <img src="{{ $details['cover_url'] }}" alt="{{ $details['title'] }}" class="w-[50px] rounded-[2px] shadow-sm shrink-0" style="object-fit: cover;">
                            <div class="flex-1">
                                <h3 class="font-serif text-[1.1rem] font-semibold text-agau-text leading-[1.3] m-0 mb-[4px]">{{ $details['title'] }}</h3>
                                <p class="text-[0.75rem] font-bold text-agau-blue tracking-[1px] uppercase mb-[8px]">{{ $details['author'] }}</p>
                                <p class="text-[0.85rem] text-agau-muted m-0">
                                    {{ $details['quantity'] }} × {{ number_format($details['price'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-[0.95rem] font-bold text-agau-text">
                                    {{ number_format($details['price'] * $details['quantity'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Résumé tarification -->
            <div class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-sm sticky top-[20px]">
                <div class="bg-agau-off-white border-b border-agau-border px-[24px] py-[16px]">
                    <h2 class="text-[0.85rem] font-bold text-agau-text uppercase tracking-[1px] m-0">Résumé du paiement</h2>
                </div>

                <div class="px-[24px] py-[24px] space-y-[16px] border-b border-agau-border">
                    <div class="flex justify-between items-center">
                        <span class="text-[0.85rem] text-agau-muted">Sous-total</span>
                        <span class="text-[0.9rem] font-bold text-agau-text">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[0.85rem] text-agau-muted">Frais de service</span>
                        <span class="text-[0.9rem] font-bold text-agau-text">0 FCFA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[0.85rem] text-agau-muted">Livraison</span>
                        <span class="text-[0.9rem] font-bold text-agau-text">Numérique</span>
                    </div>
                </div>

                <div class="px-[24px] py-[20px] bg-agau-off-white/50">
                    <div class="flex justify-between items-center mb-[24px]">
                        <span class="text-[0.9rem] font-bold text-agau-text">Total</span>
                        <span class="text-[1.4rem] font-bold text-agau-blue">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <form action="{{ route('payment.confirm', ['order' => $order->id]) }}" method="POST" class="space-y-[16px]">
                        @csrf
                        
                        <div class="space-y-3 mb-4">
                            <label class="flex items-center gap-3 p-3 border border-agau-blue bg-agau-blue/5 rounded cursor-pointer transition-colors" onclick="this.parentElement.querySelectorAll('label').forEach(l => {l.classList.remove('border-agau-blue', 'bg-agau-blue/5'); l.classList.add('border-agau-border');}); this.classList.remove('border-agau-border'); this.classList.add('border-agau-blue', 'bg-agau-blue/5');">
                                <input type="radio" name="payment_method" value="fedapay" checked class="text-agau-blue focus:ring-agau-blue w-4 h-4">
                                <div class="flex-1">
                                    <span class="block text-sm font-bold text-agau-text">Paiement en ligne (FedaPay)</span>
                                    <span class="block text-xs text-agau-muted">Mobile Money & Cartes (Afrique de l'Ouest)</span>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 border border-agau-border rounded cursor-pointer hover:bg-agau-off-white transition-colors" onclick="this.parentElement.querySelectorAll('label').forEach(l => {l.classList.remove('border-agau-blue', 'bg-agau-blue/5'); l.classList.add('border-agau-border');}); this.classList.remove('border-agau-border'); this.classList.add('border-agau-blue', 'bg-agau-blue/5');">
                                <input type="radio" name="payment_method" value="transfert_manuel" class="text-agau-blue focus:ring-agau-blue w-4 h-4">
                                <div class="flex-1">
                                    <span class="block text-sm font-bold text-agau-text">Transfert Manuel</span>
                                    <span class="block text-xs text-agau-muted">TapTap Send, Sendwave, T-Money direct...</span>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-agau-blue text-white border-none py-[14px] px-[24px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 shadow-[0_4px_12px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:-translate-y-[2px]">
                            <i class="bi bi-check2-circle"></i> Confirmer et payer
                        </button>

                        <a href="{{ route('cart.index') }}" class="w-full bg-white text-agau-text border border-agau-border py-[14px] px-[24px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all text-center no-underline hover:bg-agau-off-white inline-block">
                            Modifier le panier
                        </a>
                    </form>
                </div>

                <div class="px-[24px] py-[16px] text-[0.7rem] text-agau-muted border-t border-agau-border">
                    <p class="m-0 mb-[8px]"><i class="bi bi-lock"></i><strong>Paiement sécurisé</strong></p>
                    <p class="m-0">Vos données sont protégées par FedaPay. Aucun déblocage sans confirmation.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
