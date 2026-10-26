@extends('layouts.dashboard')

@section('title', 'Suivi de Candidature Auteur — AGAU Éditions')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-agau-border/40 pb-6">
        <div>
            <div class="flex items-center gap-2 text-agau-blue text-xs font-bold uppercase tracking-widest mb-2">
                <i class="bi bi-person-workspace"></i>
                <span>Parcours Éditorial</span>
            </div>
            <h1 class="font-serif text-3xl font-bold text-agau-text">
                Suivi de votre Candidature <span class="text-agau-blue">Auteur</span>
            </h1>
            <p class="text-xs text-agau-muted mt-1">Consultez l'état d'examen de votre manuscrit par le comité de lecture d'AGAU Éditions.</p>
        </div>

        @if($latestCandidature && in_array($latestCandidature->status, ['rejected', 'refuse']))
            <a href="{{ route('client.become-author') }}" class="inline-flex items-center gap-2 bg-agau-blue text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-agau-blue-dark transition-all no-underline shadow-sm">
                <i class="bi bi-plus-circle"></i>
                <span>Déposer un nouveau projet</span>
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
            <div>
                <p class="font-bold mb-0.5">Dossier reçu !</p>
                <p class="text-xs text-emerald-700 m-0">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <i class="bi bi-info-circle-fill text-blue-600 text-lg"></i>
            <div>
                <p class="text-xs text-blue-700 m-0">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    @if($user->isAuthor())
        <!-- Alerte Auteur Déjà Certifié -->
        <div class="bg-gradient-to-r from-agau-blue to-agau-blue-dark text-white p-6 md:p-8 rounded-2xl shadow-lg flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center md:text-left">
                <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                    <i class="bi bi-patch-check-fill text-amber-300"></i> Auteur Officiel
                </div>
                <h2 class="font-serif text-2xl font-bold">Félicitations, vous êtes auteur chez AGAU Éditions !</h2>
                <p class="text-white/80 text-xs max-w-xl">Votre dossier a été validé et votre profil auteur est actif. Vous pouvez dès maintenant gérer vos publications et suivre vos ventes depuis votre tableau de bord dédié.</p>
            </div>
            <a href="{{ route('author.dashboard') }}" class="bg-white text-agau-blue px-6 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-agau-off-white transition-all shadow-md no-underline whitespace-nowrap">
                Accéder à l'Espace Auteur <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
    @endif

    @if(!$latestCandidature)
        <!-- Aucun dossier -->
        <div class="bg-white border border-agau-border/60 rounded-2xl p-10 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-agau-blue/10 text-agau-blue flex items-center justify-center mx-auto text-2xl">
                <i class="bi bi-journal-plus"></i>
            </div>
            <h2 class="font-serif text-xl font-bold text-agau-text">Aucune candidature enregistrée</h2>
            <p class="text-xs text-agau-muted max-w-md mx-auto leading-relaxed">
                Vous n'avez pas encore soumis de projet littéraire pour intégrer le catalogue des auteurs d'AGAU Éditions.
            </p>
            <div>
                <a href="{{ route('client.become-author') }}" class="inline-flex items-center gap-2 bg-agau-blue text-white px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-agau-blue-dark transition-all no-underline shadow-sm">
                    <i class="bi bi-send"></i>
                    <span>Déposer ma candidature</span>
                </a>
            </div>
        </div>
    @else
        <!-- Stepper horizontal -->
        <div class="bg-white border border-agau-border/50 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-widest text-agau-muted mb-6">Cycle de vie de la candidature</h3>
            
            @php
                $isStep1Done = true;
                $isStep2Done = in_array($latestCandidature->status, ['analyzed', 'en_examen', 'en_cours', 'accepted', 'rejected']);
                $isStep3Done = in_array($latestCandidature->status, ['accepted']);
                $isStep4Done = $user->isAuthor();

                $isRejected = in_array($latestCandidature->status, ['rejected', 'refuse']);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                <!-- Étape 1 -->
                <div class="flex md:flex-col items-center md:items-start gap-3 p-3 rounded-xl bg-agau-blue/5 border border-agau-blue/20">
                    <div class="w-8 h-8 rounded-full bg-agau-blue text-white flex items-center justify-center text-xs font-bold">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-agau-text m-0">1. Dépôt du dossier</p>
                        <p class="text-[10px] text-agau-muted m-0">Reçu le {{ $latestCandidature->submitted_at ? $latestCandidature->submitted_at->format('d/m/Y') : $latestCandidature->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="flex md:flex-col items-center md:items-start gap-3 p-3 rounded-xl {{ $isStep2Done ? ($isRejected ? 'bg-red-50 border border-red-200' : 'bg-agau-blue/5 border border-agau-blue/20') : 'bg-gray-50 border border-gray-200 opacity-60' }}">
                    <div class="w-8 h-8 rounded-full {{ $isStep2Done ? ($isRejected ? 'bg-red-600 text-white' : 'bg-agau-blue text-white') : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-bold">
                        @if($isRejected)
                            <i class="bi bi-x-lg"></i>
                        @elseif($isStep2Done)
                            <i class="bi bi-check-lg"></i>
                        @else
                            2
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-agau-text m-0">2. Examen éditorial</p>
                        <p class="text-[10px] text-agau-muted m-0">{{ $isStep2Done ? ($isRejected ? 'Examen terminé' : 'En cours par le comité') : 'En attente de lecture' }}</p>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="flex md:flex-col items-center md:items-start gap-3 p-3 rounded-xl {{ $isStep3Done ? 'bg-agau-blue/5 border border-agau-blue/20' : 'bg-gray-50 border border-gray-200 opacity-60' }}">
                    <div class="w-8 h-8 rounded-full {{ $isStep3Done ? 'bg-agau-blue text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-bold">
                        @if($isStep3Done)
                            <i class="bi bi-check-lg"></i>
                        @else
                            3
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-agau-text m-0">3. Contrat d'édition</p>
                        <p class="text-[10px] text-agau-muted m-0">{{ $isStep3Done ? 'Contrat en préparation' : 'Proposition & Taux' }}</p>
                    </div>
                </div>

                <!-- Étape 4 -->
                <div class="flex md:flex-col items-center md:items-start gap-3 p-3 rounded-xl {{ $isStep4Done ? 'bg-emerald-50 border border-emerald-200' : 'bg-gray-50 border border-gray-200 opacity-60' }}">
                    <div class="w-8 h-8 rounded-full {{ $isStep4Done ? 'bg-emerald-600 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-bold">
                        @if($isStep4Done)
                            <i class="bi bi-check-lg"></i>
                        @else
                            4
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-agau-text m-0">4. Intronisation Auteur</p>
                        <p class="text-[10px] text-agau-muted m-0">{{ $isStep4Done ? 'Compte débloqué' : 'Signature finale' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails du dossier actuel -->
        <div class="bg-white border border-agau-border/60 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-agau-border/40">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-agau-blue">Dossier de Candidature Actif</span>
                    <h2 class="font-serif text-2xl font-bold text-agau-text m-0">{{ $latestCandidature->title }}</h2>
                    <p class="text-xs text-agau-muted">
                        Nom de plume : <span class="font-semibold text-agau-text">{{ $latestCandidature->pen_name ?: $latestCandidature->author_name }}</span>
                        • Déposé le {{ $latestCandidature->submitted_at ? $latestCandidature->submitted_at->translatedFormat('d F Y à H\hi') : $latestCandidature->created_at->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    @php
                        $badges = [
                            'pending'    => ['bg-amber-50 text-amber-700 border-amber-200', 'bi-hourglass-split', 'Reçu — En attente d\'examen'],
                            'en_cours'   => ['bg-blue-50 text-blue-700 border-blue-200', 'bi-eyeglasses', 'En cours d\'analyse éditoriale'],
                            'en_examen'  => ['bg-blue-50 text-blue-700 border-blue-200', 'bi-eyeglasses', 'En cours d\'analyse éditoriale'],
                            'analyzed'   => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'bi-check2-all', 'Analyse préliminaire effectuée'],
                            'accepted'   => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bi-check-circle-fill', 'Accepté — Contrat en cours'],
                            'rejected'   => ['bg-red-50 text-red-700 border-red-200', 'bi-x-circle-fill', 'Non retenu pour ce projet'],
                        ];
                        [$badgeClass, $badgeIcon, $badgeLabel] = $badges[$latestCandidature->status] ?? ['bg-gray-50 text-gray-700 border-gray-200', 'bi-clock', $latestCandidature->status];
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold border {{ $badgeClass }}">
                        <i class="bi {{ $badgeIcon }}"></i>
                        <span>{{ $badgeLabel }}</span>
                    </span>
                </div>
            </div>

            <!-- Grille d'informations -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-xs">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-agau-muted">Genre Littéraire</span>
                    <p class="font-semibold text-agau-text text-sm m-0">{{ $latestCandidature->category }}</p>
                </div>

                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-agau-muted">Localisation</span>
                    <p class="font-semibold text-agau-text text-sm m-0">{{ $latestCandidature->city }}, {{ $latestCandidature->country }}</p>
                </div>

                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-agau-muted">Document Manuscrit</span>
                    <p class="font-semibold text-agau-blue text-sm m-0 flex items-center gap-1">
                        <i class="bi bi-file-earmark-text"></i> Manuscrit complet transmis
                    </p>
                </div>
            </div>

            <!-- Note d'intention & Résumé -->
            <div class="space-y-4 pt-4 border-t border-agau-border/30 text-xs">
                @if($latestCandidature->intent_note)
                    <div>
                        <h4 class="font-bold text-agau-text uppercase tracking-wider text-[11px] mb-1">Note d'intention de l'auteur :</h4>
                        <p class="text-agau-muted leading-relaxed bg-agau-off-white/60 p-4 rounded-xl border border-agau-border/40">{{ $latestCandidature->intent_note }}</p>
                    </div>
                @endif

                <div>
                    <h4 class="font-bold text-agau-text uppercase tracking-wider text-[11px] mb-1">Résumé / Synopsis :</h4>
                    <p class="text-agau-muted leading-relaxed bg-agau-off-white/60 p-4 rounded-xl border border-agau-border/40">{{ $latestCandidature->synopsis }}</p>
                </div>
            </div>

            <!-- Retour éditorial / Notes admin si présentes -->
            @if($latestCandidature->admin_notes)
                <div class="p-5 rounded-xl border {{ $latestCandidature->status === 'accepted' ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : ($latestCandidature->status === 'rejected' ? 'bg-red-50/70 border-red-200 text-red-900' : 'bg-agau-blue/5 border-agau-blue/20 text-agau-text') }}">
                    <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider mb-2">
                        <i class="bi bi-chat-quote-fill"></i>
                        <span>Retour du Comité Éditorial :</span>
                    </div>
                    <p class="text-xs leading-relaxed m-0">{{ $latestCandidature->admin_notes }}</p>
                </div>
            @endif

            <!-- Explications contextuelles selon statut -->
            @if($latestCandidature->status === 'pending')
                <div class="p-4 bg-amber-50/60 border border-amber-200/80 rounded-xl text-xs text-amber-900 flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-amber-600 text-base mt-0.5"></i>
                    <div class="leading-relaxed">
                        Votre dossier est actuellement sur la table de notre comité de lecture. Les délais d'analyse oscillent habituellement entre <strong>2 et 4 semaines</strong> selon le calendrier éditorial. Vous recevrez une notification par email dès que le statut sera mis à jour.
                    </div>
                </div>
            @elseif($latestCandidature->status === 'accepted' && !$user->isAuthor())
                <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-900 flex items-start gap-3">
                    <i class="bi bi-trophy-fill text-emerald-600 text-base mt-0.5"></i>
                    <div class="leading-relaxed">
                        <strong>Félicitations !</strong> Votre projet a séduit le comité. Notre équipe prépare les clauses de votre contrat d'édition. Dès validation et signature, votre espace auteur sera automatiquement activé.
                    </div>
                </div>
            @endif
        </div>

        <!-- Historique si plusieurs candidatures -->
        @if($candidatures->count() > 1)
            <div class="space-y-4 pt-4">
                <h3 class="font-serif text-lg font-bold text-agau-text">Historique de vos soumissions</h3>
                <div class="space-y-3">
                    @foreach($candidatures->slice(1) as $oldCandidature)
                        <div class="bg-white border border-agau-border/40 rounded-xl p-4 flex items-center justify-between gap-4 text-xs">
                            <div>
                                <p class="font-bold text-agau-text m-0">{{ $oldCandidature->title }} ({{ $oldCandidature->category }})</p>
                                <p class="text-[11px] text-agau-muted m-0">Soumis le {{ $oldCandidature->submitted_at ? $oldCandidature->submitted_at->format('d/m/Y') : $oldCandidature->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $oldCandidature->status === 'accepted' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                {{ $oldCandidature->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
