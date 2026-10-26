@extends('layouts.app')

@section('title', 'Adresse de livraison — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
    <div class="max-w-[680px] mx-auto">

        <div class="mb-[48px]">
            <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Étape 2 sur 3</p>
            <h1 class="font-serif text-[2.2rem] text-agau-text font-semibold m-0">
                Adresse de <span class="text-agau-blue">livraison</span>
            </h1>
            <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-[8px] px-[20px] py-[14px] mb-[32px] flex items-start gap-3">
            <i class="bi bi-info-circle text-amber-600 text-[1.1rem] mt-[1px]"></i>
            <p class="text-[0.85rem] text-amber-700 m-0 leading-[1.6]">
                Les <strong>frais de livraison</strong> (minimum 500 FCFA) vous seront communiqués par notre équipe après validation de votre commande.
            </p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-[8px] px-[20px] py-[14px] mb-[24px]">
                <ul class="list-disc list-inside text-[0.85rem] space-y-1 m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cart.delivery.store') }}" method="POST" class="bg-white border border-agau-border rounded-[12px] p-[40px] shadow-sm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px] mb-[24px]">
                <div class="md:col-span-2">
                    <label for="nom_destinataire" class="block text-[0.75rem] font-bold tracking-[1px] uppercase text-agau-muted mb-[8px]">
                        Nom complet du destinataire <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nom_destinataire"
                        name="nom_destinataire"
                        value="{{ old('nom_destinataire', auth()->user()->name) }}"
                        placeholder="Ex : Jean Dupont"
                        class="w-full border border-agau-border rounded-[6px] px-[16px] py-[12px] text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue transition-colors @error('nom_destinataire') border-red-400 @enderror"
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="telephone_livraison" class="block text-[0.75rem] font-bold tracking-[1px] uppercase text-agau-muted mb-[8px]">
                        Numéro de téléphone <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="tel"
                        id="telephone_livraison"
                        name="telephone_livraison"
                        value="{{ old('telephone_livraison') }}"
                        placeholder="Ex : +228 90 00 00 00"
                        class="w-full border border-agau-border rounded-[6px] px-[16px] py-[12px] text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue transition-colors @error('telephone_livraison') border-red-400 @enderror"
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="adresse_livraison" class="block text-[0.75rem] font-bold tracking-[1px] uppercase text-agau-muted mb-[8px]">
                        Adresse complète <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="adresse_livraison"
                        name="adresse_livraison"
                        value="{{ old('adresse_livraison') }}"
                        placeholder="Ex : Quartier Bè, Rue des Palmiers"
                        class="w-full border border-agau-border rounded-[6px] px-[16px] py-[12px] text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue transition-colors @error('adresse_livraison') border-red-400 @enderror"
                    >
                </div>

                <div>
                    <label for="ville_livraison" class="block text-[0.75rem] font-bold tracking-[1px] uppercase text-agau-muted mb-[8px]">
                        Ville <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="ville_livraison"
                        name="ville_livraison"
                        value="{{ old('ville_livraison') }}"
                        placeholder="Ex : Lomé"
                        class="w-full border border-agau-border rounded-[6px] px-[16px] py-[12px] text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue transition-colors @error('ville_livraison') border-red-400 @enderror"
                    >
                </div>

                <div>
                    <label for="pays_livraison" class="block text-[0.75rem] font-bold tracking-[1px] uppercase text-agau-muted mb-[8px]">
                        Pays <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="pays_livraison"
                        name="pays_livraison"
                        value="{{ old('pays_livraison', 'Togo') }}"
                        placeholder="Ex : Togo"
                        class="w-full border border-agau-border rounded-[6px] px-[16px] py-[12px] text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue transition-colors @error('pays_livraison') border-red-400 @enderror"
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="notes_livraison" class="block text-[0.75rem] font-bold tracking-[1px] uppercase text-agau-muted mb-[8px]">
                        Instructions spéciales <span class="text-agau-muted font-normal">(facultatif)</span>
                    </label>
                    <textarea
                        id="notes_livraison"
                        name="notes_livraison"
                        rows="3"
                        placeholder="Ex : Sonner au portail bleu, disponible après 18h..."
                        class="w-full border border-agau-border rounded-[6px] px-[16px] py-[12px] text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue transition-colors resize-none"
                    >{{ old('notes_livraison') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-[24px] border-t border-agau-border">
                <a href="{{ route('cart.index') }}" class="text-[0.8rem] font-bold text-agau-muted uppercase tracking-[1px] no-underline hover:text-agau-blue transition-colors">
                    ← Retour au panier
                </a>
                <button
                    type="submit"
                    class="bg-agau-blue text-white border-0 px-[40px] py-[14px] rounded-[4px] text-[0.8rem] font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-colors"
                >
                    Continuer — Paiement
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
