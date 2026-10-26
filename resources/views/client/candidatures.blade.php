@extends('layouts.dashboard')

@section('title', 'Mes Candidatures — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-[32px] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Mes <span class="text-agau-blue">Candidatures</span></h1>
            <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Suivez l'état et l'évolution de vos soumissions de manuscrits (concours ECLA).</p>
        </div>
        <div>
            <a href="{{ route('candidature.step1') }}" class="inline-flex bg-agau-blue text-white px-[24px] py-[12px] rounded-full text-[0.75rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-sm">
                Nouvelle candidature
            </a>
        </div>
    </div>

    @forelse($candidatures as $candidature)
        <div class="bg-white border border-agau-border/60 rounded-[12px] p-[24px] md:p-[32px] mb-[20px] shadow-[0_4px_20px_rgba(0,0,0,0.02)] transition-all hover:shadow-[0_8px_30px_rgba(0,0,0,0.04)]">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-[20px] border-b border-agau-border/40">
                <div class="space-y-1">
                    <span class="text-[0.7rem] font-bold uppercase tracking-wider text-agau-blue-dark/50">Candidature ECLA</span>
                    <h2 class="font-serif text-[1.4rem] font-bold text-agau-text m-0">{{ $candidature->titre_manuscrit }}</h2>
                    <p class="text-[0.8rem] text-agau-muted font-medium">Soumis le {{ $candidature->created_at->translatedFormat('d F Y à H\hi') }}</p>
                </div>
                
                <div>
                    @php
                        $statusConfig = [
                            'en_attente' => ['bg-amber-50 text-amber-700 border-amber-200', 'bi-clock-history', 'Reçu / En attente'],
                            'en_cours'   => ['bg-blue-50 text-blue-700 border-blue-200', 'bi-arrow-clockwise', 'En cours d\'examen'],
                            'acceptee'   => ['bg-green-50 text-green-700 border-green-200', 'bi-check-circle', 'Accepté'],
                            'refusee'    => ['bg-red-50 text-red-700 border-red-200', 'bi-x-circle', 'Refusé'],
                        ];
                        [$statusClass, $statusIcon, $statusLabel] = $statusConfig[$candidature->statut] ?? ['bg-gray-50 text-gray-700 border-gray-200', 'bi-question', $candidature->statut];
                    @endphp
                    <span class="inline-flex items-center gap-2 px-[16px] py-[8px] rounded-full text-[0.8rem] font-bold border {{ $statusClass }}">
                        <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-[20px] text-[0.85rem]">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-agau-muted mb-1">Genre littéraire</span>
                    <span class="font-semibold text-agau-text bg-indigo-50 text-indigo-700 px-[10px] py-[4px] rounded-md text-[0.75rem] border border-indigo-100 uppercase tracking-tight">{{ $candidature->genre_litteraire }}</span>
                </div>
                
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-agau-muted mb-1">Volume de l'œuvre</span>
                    <span class="font-semibold text-agau-text">{{ number_format($candidature->nombre_mots) }} mots</span>
                </div>
                
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-agau-muted mb-1">Auteur candidat</span>
                    <span class="font-semibold text-agau-text">{{ $candidature->nom }} {{ $candidature->prenoms }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-agau-muted mb-1">Manuscrit</span>
                    <span class="font-semibold text-agau-text flex items-center gap-1.5">
                        <i class="bi bi-file-earmark-word text-blue-500"></i> Fichier déposé (.docx)
                    </span>
                </div>
            </div>

            @if($candidature->statut === 'en_attente')
                <div class="mt-[24px] p-[16px] bg-amber-50/50 border border-amber-100 rounded-[8px] text-[0.8rem] text-amber-800 leading-[1.6]">
                    <i class="bi bi-info-circle mr-1"></i> Votre manuscrit a été reçu avec succès par notre comité de lecture. Nous vous enverrons un email et mettrons à jour ce tableau de bord dès que l'analyse aura débuté.
                </div>
            @elseif($candidature->statut === 'en_cours')
                <div class="mt-[24px] p-[16px] bg-blue-50/50 border border-blue-100 rounded-[8px] text-[0.8rem] text-blue-800 leading-[1.6]">
                    <i class="bi bi-info-circle mr-1"></i> Notre comité examine actuellement votre manuscrit. Cette phase peut prendre de quelques semaines à plusieurs mois selon le volume d'œuvres soumises. Merci de votre patience.
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white border border-agau-border rounded-[10px] p-[60px] text-center">
            <div class="w-[80px] h-[80px] bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-[24px]">
                <i class="bi bi-pencil"></i>
            </div>
            <h2 class="text-[1.2rem] font-bold text-agau-text mb-[12px]">Aucune candidature trouvée</h2>
            <p class="text-agau-muted max-w-[460px] mx-auto text-[0.9rem] leading-[1.6]">
                Vous n'avez pas encore déposé de candidature pour l'appel à manuscrits ECLA avec cette adresse email ({{ Auth::user()->email }}).
            </p>
            <a href="{{ route('candidature.step1') }}" class="inline-flex bg-agau-blue text-white px-[28px] py-[12px] rounded-full text-[0.75rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all mt-[24px] shadow-sm">
                Déposer une candidature
            </a>
        </div>
    @endforelse
</div>
@endsection
