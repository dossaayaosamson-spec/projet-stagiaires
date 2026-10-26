@extends('layouts.app')

@section('title', 'Instructions de paiement manuel — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
    <div class="max-w-[800px] mx-auto">
        <div class="text-center mb-[48px]">
            <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Paiement</p>
            <h1 class="font-serif text-[2.4rem] text-agau-text font-semibold m-0">Transfert <span class="text-agau-blue">Manuel</span></h1>
            <div class="w-[40px] h-[3px] bg-agau-blue mx-auto mt-[14px] rounded-[2px]"></div>
        </div>

        <div class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-sm p-6 md:p-10">
            <h2 class="text-xl font-bold mb-4">Étape 1 : Effectuez le transfert</h2>
            <p class="text-agau-muted mb-6">
                Veuillez envoyer le montant total de votre commande via votre service de transfert préféré (TapTap Send, Sendwave, T-Money direct, etc.) aux coordonnées ci-dessous :
            </p>

            <div class="bg-agau-off-white p-6 rounded-lg mb-8 border border-agau-border text-center">
                <p class="text-sm text-agau-muted uppercase tracking-wider mb-2">Montant à envoyer</p>
                <p class="text-3xl font-bold text-agau-blue mb-6">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>

                <p class="text-sm text-agau-muted uppercase tracking-wider mb-2">Numéro T-Money (Togo)</p>
                <p class="text-2xl font-bold text-agau-text tracking-widest">+228 70 76 76 94</p>
            </div>

            <h2 class="text-xl font-bold mb-4">Étape 2 : Confirmez votre paiement</h2>
            <p class="text-agau-muted mb-6">
                Une fois le transfert effectué, veuillez uploader une <strong>capture d'écran de votre reçu</strong> (ou confirmation de transfert) ci-dessous. Nous validerons votre commande dès réception.
            </p>

            <form action="{{ route('payment.manual.proof', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="transaction_proof" class="block text-sm font-bold text-agau-text mb-2">Capture d'écran du reçu *</label>
                    <input type="file" name="transaction_proof" id="transaction_proof" accept="image/png, image/jpeg, image/jpg" required
                        class="w-full px-4 py-3 border border-agau-border rounded-md focus:outline-none focus:ring-2 focus:ring-agau-blue focus:border-transparent text-sm">
                    @error('transaction_proof')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-agau-muted mt-2">Formats acceptés : JPG, PNG. Taille max : 5 Mo.</p>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-agau-blue text-white border-none py-[14px] px-[24px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 shadow-[0_4px_12px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:-translate-y-[2px]">
                        <i class="bi bi-upload mr-2"></i> Envoyer la preuve
                    </button>
                    <a href="{{ route('client.orders') }}" class="bg-white text-agau-text border border-agau-border py-[14px] px-[24px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all text-center no-underline hover:bg-agau-off-white inline-block">
                        Plus tard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
