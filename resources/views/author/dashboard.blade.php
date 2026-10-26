@extends('layouts.dashboard')

@section('title', 'Espace Auteur — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <!-- Header -->
    <div class="mb-[32px] flex flex-col md:flex-row md:items-end justify-between gap-[16px]">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-agau-blue/10 text-agau-blue text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Espace Auteur</span>
                <span class="text-[10px] text-agau-muted font-medium">•</span>
                <span class="text-[10px] text-agau-muted font-medium">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <h1 class="font-serif text-[2rem] text-agau-text font-bold m-0">Ravi de vous revoir, <span class="text-agau-blue">{{ auth()->user()->prenom }}</span></h1>
            <p class="text-[0.85rem] text-agau-muted mt-1 font-medium">Voici un aperçu de vos publications et de vos performances littéraires.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('author.profile') }}" class="bg-white text-agau-text border border-agau-border py-2.5 px-5 text-[0.75rem] font-bold tracking-wider uppercase cursor-pointer rounded-lg transition-all hover:bg-agau-off-white no-underline flex items-center gap-2">
                <i class="bi bi-person-badge"></i>
                Mon profil
            </a>
            <button type="button" onclick="document.getElementById('manuscriptModal').classList.remove('hidden')" class="bg-agau-blue text-white border-none py-2.5 px-5 text-[0.75rem] font-bold tracking-wider uppercase cursor-pointer rounded-lg transition-all shadow-lg shadow-agau-blue/20 hover:bg-[#1B5E8A] no-underline flex items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                Soumettre un manuscrit
            </button>
        </div>
    </div>

    <!-- Stats Grid (Bento Style : Chiffre d'affaires brut, Royalties perçues, Solde disponible, Total versé) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- CA Brut Ventes -->
        <div class="bg-white border border-agau-border/40 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="bi bi-graph-up-arrow text-xl"></i>
            </div>
            <div class="text-3xl font-black text-agau-text mb-1">{{ $stats['total_sales'] }} <span class="text-xs font-bold text-agau-muted">FCFA</span></div>
            <div class="text-[0.7rem] text-agau-muted font-bold uppercase tracking-widest">CA Brut Ventes</div>
            <div class="mt-4 flex items-center gap-2 text-[0.65rem] text-blue-600 font-bold">
                <span>Total commercialisé</span>
            </div>
        </div>

        <!-- Royalties totales accumulées -->
        <div class="bg-white border border-agau-border/40 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="bi bi-award text-xl"></i>
            </div>
            <div class="text-3xl font-black text-agau-text mb-1">{{ $stats['total_gagne'] }} <span class="text-xs font-bold text-agau-muted">FCFA</span></div>
            <div class="text-[0.7rem] text-agau-muted font-bold uppercase tracking-widest">Droits d'auteur perçus</div>
            <div class="mt-4 flex items-center gap-2 text-[0.65rem] text-indigo-600 font-bold">
                <span>Royalties contractuelles</span>
            </div>
        </div>

        <!-- Solde disponible pour retrait -->
        <div class="bg-gradient-to-br from-agau-blue to-agau-blue-dark text-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all group relative overflow-hidden">
            <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="bi bi-wallet2 text-xl"></i>
            </div>
            <div class="text-3xl font-black text-white mb-1">{{ $stats['solde_disponible'] }} <span class="text-xs font-bold text-white/70">FCFA</span></div>
            <div class="text-[0.7rem] text-white/80 font-bold uppercase tracking-widest">Solde Disponible</div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-[0.65rem] text-white/80 font-bold">Prêt à retirer</span>
                @if($stats['raw_solde'] >= 10000)
                    <button type="button" onclick="document.getElementById('payoutModal').classList.remove('hidden')" class="bg-white text-agau-blue px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-agau-off-white transition-all border-none cursor-pointer">
                        Retirer
                    </button>
                @else
                    <span class="text-[9px] text-white/60 italic">Min. 10 000 FCFA</span>
                @endif
            </div>
        </div>

        <!-- Droits déjà versés -->
        <div class="bg-white border border-agau-border/40 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="bi bi-check2-all text-xl"></i>
            </div>
            <div class="text-3xl font-black text-agau-text mb-1">{{ $stats['total_retire'] }} <span class="text-xs font-bold text-agau-muted">FCFA</span></div>
            <div class="text-[0.7rem] text-agau-muted font-bold uppercase tracking-widest">Total Déjà Versé</div>
            <div class="mt-4 flex items-center gap-2 text-[0.65rem] text-emerald-600 font-bold">
                <i class="bi bi-shield-check"></i>
                <span>Retraits acquittés</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-8">
        <div class="space-y-6">
            <!-- Mes manuscrits soumis -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-agau-border/10 flex justify-between items-center bg-agau-off-white/30">
                    <h2 class="text-[1.1rem] font-bold text-agau-text flex items-center gap-2 m-0">
                        <i class="bi bi-journal-text text-agau-blue"></i>
                        Mes manuscrits soumis
                    </h2>
                    <span class="text-[0.65rem] font-bold text-agau-muted uppercase tracking-wider">{{ $manuscripts->count() }} soumissions</span>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($manuscripts as $manuscript)
                            <div class="flex items-center justify-between gap-4 p-4 rounded-xl border border-agau-border/30 bg-[#fcfcfc] hover:bg-white hover:border-agau-blue/30 transition-all">
                                <div class="min-w-0">
                                    <h4 class="text-[0.95rem] font-bold text-agau-text m-0 mb-1 truncate">{{ $manuscript->title ?? 'Manuscrit sans titre' }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 text-[0.7rem] text-agau-muted">
                                        <span>{{ $manuscript->category ?? 'Catégorie non précisée' }}</span>
                                        <span>•</span>
                                        <span>{{ $manuscript->submitted_at ? $manuscript->submitted_at->translatedFormat('d/m/Y') : 'Date inconnue' }}</span>
                                    </div>
                                </div>
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'analyzed' => 'bg-blue-100 text-blue-700',
                                        'accepted' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ][$manuscript->status] ?? 'bg-gray-100 text-gray-700';
                                    $statusLabel = [
                                        'pending' => 'En attente',
                                        'analyzed' => 'Analyse en cours',
                                        'accepted' => 'Accepté',
                                        'rejected' => 'Refusé',
                                    ][$manuscript->status] ?? ucfirst($manuscript->status ?? 'Inconnu');
                                @endphp
                                <span class="{{ $statusClass }} px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight whitespace-nowrap">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        @empty
                            <div class="py-12 text-center bg-agau-off-white/30 rounded-xl border border-dashed border-agau-border/60">
                                <i class="bi bi-journal-text text-3xl text-agau-muted/20 mb-3 block"></i>
                                <p class="text-agau-muted text-[0.85rem] italic">Vous n'avez pas encore soumis de manuscrit.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Mes Ouvrages -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-agau-border/10 flex justify-between items-center bg-agau-off-white/30">
                    <h2 class="text-[1.1rem] font-bold text-agau-text flex items-center gap-2 m-0">
                        <i class="bi bi-journals text-agau-blue"></i>
                        Mes Publications
                    </h2>
                    <span class="text-[0.65rem] font-bold text-agau-muted uppercase tracking-wider">{{ count($authorBooks) }} ouvrages</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($authorBooks as $book)
                        <div class="group flex gap-4 items-center p-4 rounded-xl border border-agau-border/30 bg-[#fcfcfc] hover:bg-white hover:border-agau-blue/30 transition-all cursor-default">
                            <div class="w-[60px] aspect-[2/3] rounded-lg overflow-hidden shadow-sm bg-agau-off-white flex-shrink-0 group-hover:shadow-md transition-all">
                                @if($book->cover_path)
                                    <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->titre }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full" @style(['background-color: ' . ($book->couleur_couverture ?? '#e5e7eb') => true])></div>
                                @endif
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <h4 class="text-[0.95rem] font-bold text-agau-text m-0 mb-1 truncate group-hover:text-agau-blue transition-colors">{{ $book->titre }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-[0.75rem] text-agau-muted font-medium">{{ number_format($book->prix, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-[10px] text-agau-muted/30">•</span>
                                    <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-full {{ $book->disponibilite === 'disponible' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ ucfirst($book->disponibilite) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-2 py-12 text-center bg-agau-off-white/30 rounded-xl border border-dashed border-agau-border/60">
                            <i class="bi bi-book text-3xl text-agau-muted/20 mb-3 block"></i>
                            <p class="text-agau-muted text-[0.85rem] italic">Vous n'avez pas encore d'ouvrages publiés sur la plateforme.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- Mes Contrats d'édition -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-agau-border/10 flex justify-between items-center bg-agau-off-white/30">
                    <h2 class="text-[1.1rem] font-bold text-agau-text flex items-center gap-2 m-0">
                        <i class="bi bi-file-earmark-check text-agau-blue"></i>
                        Mes Contrats & Taux de Redevance
                    </h2>
                    <span class="text-[0.65rem] font-bold text-agau-muted uppercase tracking-wider">{{ $contracts->count() }} contrat{{ $contracts->count() > 1 ? 's' : '' }}</span>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($contracts as $contract)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-agau-border/30 bg-[#fcfcfc] hover:bg-white transition-all">
                            <div>
                                <h4 class="text-sm font-bold text-agau-text m-0 mb-1">
                                    {{ $contract->book?->titre ?? 'Contrat général / Publication à venir' }}
                                </h4>
                                <div class="text-xs text-agau-muted flex items-center gap-2">
                                    <span>Type : <strong>{{ $contract->type_contrat === 'compte_editeur' ? 'Compte d\'Éditeur' : 'Distribution Numérique' }}</strong></span>
                                    <span>•</span>
                                    <span>Depuis le {{ $contract->date_debut ? $contract->date_debut->format('d/m/Y') : '-' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-black text-agau-blue">{{ number_format($contract->taux_redevance, 1) }}%</span>
                                <div class="text-[10px] text-agau-muted uppercase font-bold">Royalties</div>
                            </div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-agau-muted text-xs italic bg-agau-off-white/30 rounded-xl">
                            Aucun contrat formalisé pour l'instant.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Historique des Demandes de Versement -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-agau-border/10 flex justify-between items-center bg-agau-off-white/30">
                    <h2 class="text-[1.1rem] font-bold text-agau-text flex items-center gap-2 m-0">
                        <i class="bi bi-cash-coin text-emerald-600"></i>
                        Mes Demandes de Versement (Retraits)
                    </h2>
                    @if($stats['raw_solde'] >= 10000)
                    <button type="button" onclick="document.getElementById('payoutModal').classList.remove('hidden')" class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-700 transition-all border-none cursor-pointer">
                        Demander un retrait
                    </button>
                    @endif
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($payoutRequests as $payout)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-agau-border/30 bg-[#fcfcfc]">
                            <div>
                                <div class="text-sm font-bold text-agau-text">{{ number_format($payout->montant, 0, ',', ' ') }} FCFA</div>
                                <div class="text-xs text-agau-muted mt-0.5">
                                    {{ match($payout->moyen_paiement) {
                                        'tmoney' => 'T-Money',
                                        'flooz' => 'Flooz',
                                        default => 'Virement bancaire'
                                    } }} — {{ $payout->created_at ? $payout->created_at->format('d/m/Y') : '-' }}
                                </div>
                                @if($payout->reference_transaction)
                                    <div class="text-[11px] text-emerald-700 mt-1 font-mono">Réf: {{ $payout->reference_transaction }}</div>
                                @endif
                                @if($payout->admin_notes)
                                    <div class="text-[11px] text-agau-muted mt-1 italic">{{ $payout->admin_notes }}</div>
                                @endif
                            </div>
                            <div>
                                @php
                                    $payoutBadge = match($payout->statut) {
                                        'valide' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejete' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                    $payoutLabel = match($payout->statut) {
                                        'valide' => 'Versé',
                                        'rejete' => 'Refusé',
                                        default => 'En traitement',
                                    };
                                @endphp
                                <span class="{{ $payoutBadge }} border px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                    {{ $payoutLabel }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-agau-muted text-xs italic bg-agau-off-white/30 rounded-xl">
                            Aucun versement demandé pour le moment.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Activity -->
        <div class="space-y-6">
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-agau-border/10 bg-agau-off-white/30">
                    <h2 class="text-[0.95rem] font-bold text-agau-text m-0 flex items-center gap-2">
                        <i class="bi bi-lightning-charge text-amber-500"></i>
                        Activité & Aide
                    </h2>
                </div>
                <div class="p-5">
                    <div class="space-y-4 text-xs text-agau-text">
                        <div class="p-3 bg-agau-border/5 rounded-xl border border-agau-border/10">
                            <span class="font-bold text-agau-blue block mb-1">Règles de versement</span>
                            <p class="text-agau-muted m-0 leading-relaxed">
                                Les versements sont possibles à partir d'un seuil minimal de <strong>10 000 FCFA</strong>. Traitement sous 24h à 48h ouvrées.
                            </p>
                        </div>
                        <div class="p-3 bg-agau-border/5 rounded-xl border border-agau-border/10">
                            <span class="font-bold text-agau-blue block mb-1">Moyens acceptés</span>
                            <p class="text-agau-muted m-0 leading-relaxed">
                                T-Money (Togocom), Flooz (Moov Togo) ou Virement direct.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotion Card -->
            <div class="bg-agau-blue rounded-2xl p-6 text-white relative overflow-hidden shadow-lg shadow-agau-blue/20">
                <div class="relative z-10">
                    <h3 class="text-sm font-bold mb-2 uppercase tracking-widest">Besoin d'aide ?</h3>
                    <p class="text-xs text-blue-100 mb-4 leading-relaxed">Notre équipe éditoriale est à votre disposition pour vous accompagner dans vos projets.</p>
                    <a href="mailto:editions@agau.com" class="inline-block bg-white text-agau-blue px-4 py-2 rounded-lg text-[0.7rem] font-bold uppercase tracking-wider hover:bg-blue-50 transition-colors no-underline">Nous contacter</a>
                </div>
                <i class="bi bi-patch-question absolute -bottom-4 -right-4 text-7xl text-white/10 rotate-12"></i>
            </div>
        </div>
    </div>
</div>

<div id="manuscriptModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background: rgba(15, 23, 42, 0.55);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl border border-agau-border/40">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-[0.62rem] font-bold tracking-[3px] uppercase mb-2" style="color:#1D6FB8;">Soumission</p>
                    <h3 class="font-serif text-[1.6rem] font-semibold mb-1" style="color:#0D3B64;">Déposer un manuscrit</h3>
                </div>
                <button type="button" onclick="document.getElementById('manuscriptModal').classList.add('hidden')" class="text-2xl text-gray-400 hover:text-red-500 bg-transparent border-none cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('manuscripts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="title" class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Titre du manuscrit</label>
                        <input id="title" type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border:1.5px solid #E2E8F0; background:#F8FAFC;" required>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Catégorie</label>
                        <input id="category" type="text" name="category" value="{{ old('category') }}" placeholder="Roman, poésie, essai..." class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border:1.5px solid #E2E8F0; background:#F8FAFC;">
                    </div>

                    <div>
                        <label for="synopsis" class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Synopsis</label>
                        <textarea id="synopsis" name="synopsis" rows="4" class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border:1.5px solid #E2E8F0; background:#F8FAFC;">{{ old('synopsis') }}</textarea>
                    </div>

                    <div>
                        <label for="document" class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Fichier</label>
                        <input id="document" type="file" name="document" accept=".pdf,.docx,.doc,.txt" class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border:1.5px solid #E2E8F0; background:#F8FAFC;" required>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('manuscriptModal').classList.add('hidden')" class="px-4 py-2.5 border border-agau-border/40 rounded-lg text-sm font-bold text-agau-muted hover:bg-agau-border/5 transition-all cursor-pointer">Annuler</button>
                    <button type="submit" class="px-5 py-2.5 bg-agau-blue text-white rounded-lg text-xs font-bold tracking-wider uppercase hover:bg-[#1B5E8A] transition-all shadow-lg shadow-agau-blue/20 cursor-pointer">Soumettre</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Demande de Retrait -->
<div id="payoutModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background: rgba(15, 23, 42, 0.55);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-agau-border/40">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-[0.62rem] font-bold tracking-[3px] uppercase mb-1 text-emerald-600">Finances</p>
                    <h3 class="font-serif text-[1.4rem] font-semibold text-agau-text m-0">Demander un versement</h3>
                </div>
                <button type="button" onclick="document.getElementById('payoutModal').classList.add('hidden')" class="text-2xl text-gray-400 hover:text-red-500 bg-transparent border-none cursor-pointer">&times;</button>
            </div>

            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 text-xs text-emerald-800 mb-5 flex items-center justify-between">
                <span>Solde disponible :</span>
                <span class="font-black text-sm">{{ $stats['solde_disponible'] }} FCFA</span>
            </div>

            <form action="{{ route('author.payout.request') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-1">Montant à retirer (FCFA) *</label>
                    <input type="number" name="montant" min="10000" max="{{ $stats['raw_solde'] }}" step="500" value="{{ old('montant', min(10000, $stats['raw_solde'])) }}" class="w-full px-4 py-2.5 rounded-xl border border-agau-border/50 text-sm focus:outline-none focus:border-agau-blue" required>
                    <span class="text-[10px] text-agau-muted mt-1 block">Seuil minimal : 10 000 FCFA</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-1">Moyen de paiement *</label>
                    <select name="moyen_paiement" class="w-full px-4 py-2.5 rounded-xl border border-agau-border/50 text-sm bg-white" required>
                        <option value="tmoney">T-Money (Togocom)</option>
                        <option value="flooz">Flooz (Moov Togo)</option>
                        <option value="virement_bancaire">Virement bancaire (UEMOA / International)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-1">Coordonnées de paiement *</label>
                    <textarea name="coordonnees_paiement" rows="3" placeholder="Numéro de téléphone avec indicatif (ex: +228 90 XX XX XX) ou RIB complet avec nom du titulaire..." class="w-full px-4 py-2.5 rounded-xl border border-agau-border/50 text-sm focus:outline-none focus:border-agau-blue" required>{{ old('coordonnees_paiement') }}</textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-agau-border/20">
                    <button type="button" onclick="document.getElementById('payoutModal').classList.add('hidden')" class="px-4 py-2.5 border border-agau-border/40 rounded-lg text-sm font-bold text-agau-muted hover:bg-agau-border/5 transition-all cursor-pointer">Annuler</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-xs font-bold tracking-wider uppercase hover:bg-emerald-700 transition-all shadow-md cursor-pointer">Confirmer la demande</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
