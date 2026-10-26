@extends('layouts.dashboard')

@section('title', 'Confirmer votre abonnement — AGAU Éditions')

@section('content')
<div class="max-w-[800px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Confirmer votre <span class="text-agau-blue">Abonnement</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Vérifiez les détails avant de confirmer.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-[24px]">
        <!-- Détails de l'abonnement -->
        <div class="bg-white border border-agau-border rounded-[10px] p-[32px] shadow-[0_4px_20px_rgba(15,74,122,0.03)]">
            <h2 class="text-[1.3rem] font-bold text-agau-text mb-[24px] m-0">Détails du plan</h2>

            <div class="space-y-[20px] mb-[32px]">
                <div class="pb-[20px] border-b border-agau-border/50">
                    <p class="text-[0.7rem] font-bold uppercase text-agau-muted tracking-[1px] mb-[8px] m-0">Plan sélectionné</p>
                    <h3 class="text-[1.4rem] font-bold text-agau-text m-0">{{ $selectedPlan['nom'] }}</h3>
                </div>

                <div class="pb-[20px] border-b border-agau-border/50">
                    <p class="text-[0.7rem] font-bold uppercase text-agau-muted tracking-[1px] mb-[8px] m-0">Durée</p>
                    <p class="text-[0.95rem] text-agau-text m-0">
                        {{ match($selectedPlan['nom']) {
                            'Hebdomadaire' => '7 jours',
                            'Mensuel' => '30 jours (1 mois)',
                            'Trimestriel' => '90 jours (3 mois)',
                            'Semestriel' => '180 jours (6 mois)',
                            'Annuel' => '365 jours (1 an)',
                            default => 'Durée personnalisée'
                        } }}
                    </p>
                </div>

                <div class="pb-[20px] border-b border-agau-border/50">
                    <p class="text-[0.7rem] font-bold uppercase text-agau-muted tracking-[1px] mb-[8px] m-0">Date de début</p>
                    <p class="text-[0.95rem] text-agau-text m-0">{{ now()->format('d/m/Y à H:i') }}</p>
                </div>

                <div>
                    <p class="text-[0.7rem] font-bold uppercase text-agau-muted tracking-[1px] mb-[8px] m-0">Date d'expiration</p>
                    <p class="text-[0.95rem] text-agau-text m-0">
                        {{ now()->addDays(
                            match($selectedPlan['nom']) {
                                'Hebdomadaire' => 7,
                                'Mensuel' => 30,
                                'Trimestriel' => 90,
                                'Semestriel' => 180,
                                'Annuel' => 365,
                                default => 30
                            }
                        )->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="space-y-[12px] p-[16px] bg-agau-blue/5 rounded-[8px] mb-[32px]">
                <h4 class="text-[0.85rem] font-bold text-agau-blue uppercase tracking-[1px] m-0 mb-[12px]">✨ Avantages inclus</h4>
                <ul class="list-none p-0 m-0 space-y-[8px]">
                    @foreach($selectedPlan['avantages'] as $avantage)
                    <li class="flex items-start gap-[8px] text-[0.8rem] text-agau-text">
                        <span class="text-[14px] shrink-0">✓</span>
                        {{ $avantage }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <form action="{{ route('subscription.confirm') }}" method="POST" class="space-y-[12px]">
                @csrf
                <input type="hidden" name="plan" value="{{ $selectedPlan['nom'] }}">

                <button type="submit" class="w-full bg-agau-blue text-white border-none py-[14px] px-[24px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 shadow-[0_4px_12px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:-translate-y-[2px]">
                    ✓ Confirmer l'abonnement
                </button>

                <a href="{{ route('home') }}#offres" class="w-full bg-white text-agau-text border border-agau-border py-[14px] px-[24px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all text-center no-underline hover:bg-agau-off-white inline-block">
                    Changer de plan
                </a>
            </form>

            <p class="text-[0.7rem] text-agau-muted mt-[16px] p-[12px] bg-agau-off-white rounded-[6px] m-0">
                💡 Vous pourrez modifier votre abonnement ou l'annuler à tout moment depuis votre profil.
            </p>
        </div>

        <!-- Résumé tarification -->
        <div class="h-fit bg-white border border-agau-border rounded-[10px] overflow-hidden shadow-[0_4px_20px_rgba(15,74,122,0.03)]">
            <div class="bg-agau-off-white border-b border-agau-border px-[24px] py-[16px]">
                <h3 class="text-[0.85rem] font-bold text-agau-text uppercase tracking-[1px] m-0">Résumé du paiement</h3>
            </div>

            <div class="px-[24px] py-[24px] space-y-[16px]">
                <div class="flex justify-between items-center pb-[16px] border-b border-agau-border/50">
                    <span class="text-[0.85rem] text-agau-muted">Montant</span>
                    <span class="text-[0.9rem] font-bold text-agau-text">{{ number_format($selectedPlan['prix'], 0, ',', ' ') }} FCFA</span>
                </div>

                <div class="flex justify-between items-center pb-[16px] border-b border-agau-border/50">
                    <span class="text-[0.85rem] text-agau-muted">Frais</span>
                    <span class="text-[0.9rem] font-bold text-agau-text">0 FCFA</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-[0.95rem] font-bold text-agau-text">Total</span>
                    <span class="text-[1.4rem] font-bold text-agau-blue">{{ number_format($selectedPlan['prix'], 0, ',', ' ') }} FCFA</span>
                </div>
            </div>

            <div class="px-[24px] py-[16px] bg-agau-off-white/50 border-t border-agau-border">
                <p class="text-[0.7rem] text-agau-muted m-0">
                    🔒 Paiement sécurisé via <strong>FedaPay</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
