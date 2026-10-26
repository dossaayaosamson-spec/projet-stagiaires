@extends('layouts.app')

@section('title', 'Merci pour votre achat ! — AGAU Éditions')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-[60px] px-[20px]">
    <div class="max-w-[600px] w-full bg-white border border-agau-border rounded-[20px] p-[40px] shadow-[0_10px_40px_rgba(0,0,0,0.05)] text-center animate-fade-in">
        <!-- Success Icon -->
        <div class="mb-[32px] flex justify-center">
            <div class="w-[100px] h-[100px] bg-green-50 rounded-full flex items-center justify-center text-[3.5rem] animate-bounce-slow">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
        </div>

        <h1 class="font-serif text-[2.2rem] text-agau-text font-bold m-0 mb-[16px]">Merci pour votre confiance !</h1>
        <p class="text-[1.1rem] text-agau-muted mb-[32px] leading-[1.6]">
            Votre paiement a été accepté avec succès. Votre commande <span class="font-bold text-agau-blue">#{{ $order->id }}</span> est maintenant confirmée.
        </p>

        <!-- Order Summary -->
        <div class="bg-agau-off-white rounded-[12px] p-[24px] mb-[40px] text-left">
            <h3 class="text-[0.8rem] font-bold text-agau-text uppercase tracking-[1px] mb-[16px] border-b border-agau-border pb-[8px]">Résumé de la commande</h3>
            <div class="space-y-[12px]">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center text-[0.95rem]">
                    <span class="text-agau-text font-medium">{{ $item->book->titre }}</span>
                    <span class="text-agau-muted">{{ number_format($item->prix, 0, ',', ' ') }} FCFA</span>
                </div>
                @endforeach
                <div class="pt-[12px] mt-[12px] border-t border-agau-border flex justify-between items-center font-bold text-[1.1rem] text-agau-blue">
                    <span>Total payé</span>
                    <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-[16px] justify-center">
            <a href="{{ route('client.library') }}" class="bg-agau-blue text-white py-[14px] px-[32px] rounded-[8px] text-[0.85rem] font-bold tracking-[1px] uppercase hover:bg-[#1B5E8A] transition-all no-underline shadow-lg shadow-agau-blue/20">
                Accéder à ma bibliothèque
            </a>
            <a href="{{ route('home') }}" class="bg-agau-off-white text-agau-text py-[14px] px-[32px] rounded-[8px] text-[0.85rem] font-bold tracking-[1px] uppercase hover:bg-agau-border transition-all no-underline border border-agau-border">
                Retour à l'accueil
            </a>
        </div>

        <p class="mt-[40px] text-[0.8rem] text-agau-muted">
            Un email de confirmation vient de vous être envoyé à <span class="font-semibold">{{ auth()->user()->email }}</span>.
        </p>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
    
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce-slow { animation: bounce-slow 2s infinite ease-in-out; }
    
    .bg-agau-off-white { background-color: #F8FAFC; }
    .border-agau-border { border-color: #E2E8F0; }
    .text-agau-text { color: #1E293B; }
    .text-agau-muted { color: #64748B; }
    .text-agau-blue { color: #1B6CA8; }
    .bg-agau-blue { background-color: #1B6CA8; }
</style>
@endsection
