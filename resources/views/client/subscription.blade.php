@extends('layouts.dashboard')

@section('title', 'Mon Abonnement — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Gérer mon <span class="text-agau-blue">Abonnement</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Consultez les détails de votre offre actuelle.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-[24px]">
        <div class="bg-white border border-agau-border rounded-[10px] p-[32px] shadow-[0_4px_20px_rgba(15,74,122,0.03)] flex flex-col items-center justify-center text-center">
            @if($subscription)
                <div class="w-[80px] h-[80px] bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-[24px]">
                    <i class="bi bi-credit-card text-[2.5rem] text-agau-blue"></i>
                </div>
                <h2 class="text-[1.2rem] font-bold text-agau-text mb-[8px]">Plan {{ ucfirst($subscription->plan) }}</h2>
                <span class="text-[0.65rem] font-bold px-[12px] py-[4px] rounded-[50px] text-[#1D9E75] bg-[#1D9E75]/10 uppercase mb-[24px]">Actif</span>
                <p class="text-agau-muted max-w-[400px] mx-auto mb-[32px] text-[0.9rem] leading-[1.6]">
                    Votre abonnement expire le {{ $subscription->date_fin->format('d/m/Y') }}. Profitez de tout notre catalogue sans limite.
                </p>
                <div class="flex gap-[16px]">
                    <a href="{{ route('home') }}#offres" class="bg-agau-blue text-white no-underline py-[12px] px-[24px] text-[0.7rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 shadow-[0_4px_12px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:-translate-y-[2px] inline-block">Changer de formule</a>
                    <button class="bg-white border border-agau-border text-agau-muted py-[12px] px-[24px] text-[0.7rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 hover:bg-agau-off-white">Annuler</button>
                </div>
            @else
                <div class="w-[80px] h-[80px] bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-[24px]">
                    <i class="bi bi-credit-card text-[2.5rem] text-agau-blue"></i>
                </div>
                <h2 class="text-[1.2rem] font-bold text-agau-text mb-[12px]">Aucun abonnement actif</h2>
                <p class="text-agau-muted max-w-[400px] mx-auto mb-[32px] text-[0.9rem] leading-[1.6]">
                    Découvrez nos offres pour accéder en illimité à toute notre bibliothèque et soutenir les auteurs.
                </p>
                <a href="{{ route('home') }}#offres" class="inline-block bg-agau-blue text-white no-underline py-[12px] px-[32px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] shadow-[0_4px_12px_rgba(27,108,168,0.25)] transition-all hover:bg-agau-blue-dark">Voir les offres</a>
            @endif
        </div>

        <div class="bg-white border border-agau-border rounded-[10px] p-[24px] shadow-[0_4px_20px_rgba(15,74,122,0.03)]">
            <h3 class="text-[1rem] font-bold text-agau-text mb-[20px] flex items-center gap-[10px]"><i class="bi bi-info-circle text-agau-blue"></i> Pourquoi s'abonner ?</h3>
            <ul class="list-none p-0 m-0 space-y-[16px]">
                <li class="flex items-start gap-[12px] text-[0.85rem] text-agau-muted leading-[1.5]">
                    <i class="bi bi-check2 text-agau-blue shrink-0"></i>
                    Accès illimité à tous nos romans et essais.
                </li>
                <li class="flex items-start gap-[12px] text-[0.85rem] text-agau-muted leading-[1.5]">
                    <i class="bi bi-check2 text-agau-blue shrink-0"></i>
                    Lecture hors-ligne via l'application.
                </li>
                <li class="flex items-start gap-[12px] text-[0.85rem] text-agau-muted leading-[1.5]">
                    <i class="bi bi-check2 text-agau-blue shrink-0"></i>
                    Rencontres exclusives avec les auteurs.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
