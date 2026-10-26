@extends('layouts.admin')

@section('title', 'Versements Auteurs — Admin AGAU Éditions')
@section('header-title', 'Versements Auteurs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Demandes de versement (Auteurs)</h1>
            <p class="text-sm text-agau-muted font-medium">Validez les retraits de redevances et suivez les décaissements Mobile Money / Virement.</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border border-agau-border/40 rounded-2xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <p class="text-xs uppercase font-bold text-agau-muted tracking-wider">En attente</p>
                <p class="text-2xl font-bold text-agau-text">{{ $stats['en_attente'] }}</p>
            </div>
        </div>

        <div class="bg-white border border-agau-border/40 rounded-2xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-agau-blue flex items-center justify-center text-xl">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <p class="text-xs uppercase font-bold text-agau-muted tracking-wider">Montant en attente</p>
                <p class="text-2xl font-bold text-agau-blue">{{ number_format($stats['montant_attente'], 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <div class="bg-white border border-agau-border/40 rounded-2xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <p class="text-xs uppercase font-bold text-agau-muted tracking-wider">Total versé (Historique)</p>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_verse'], 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <form method="GET" class="bg-white border border-agau-border/40 rounded-xl p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Recherche</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-agau-muted/60 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nom d'auteur ou référence..."
                    class="w-full pl-9 pr-4 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all">
            </div>
        </div>

        <div class="min-w-40">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Statut</label>
            <select name="statut" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm bg-white">
                <option value="">Tous les statuts</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validé / Payé</option>
                <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
            </select>
        </div>

        <div class="min-w-44">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Moyen de paiement</label>
            <select name="moyen_paiement" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm bg-white">
                <option value="">Tous les moyens</option>
                <option value="tmoney" {{ request('moyen_paiement') === 'tmoney' ? 'selected' : '' }}>T-Money (Togocom)</option>
                <option value="flooz" {{ request('moyen_paiement') === 'flooz' ? 'selected' : '' }}>Flooz (Moov)</option>
                <option value="virement_bancaire" {{ request('moyen_paiement') === 'virement_bancaire' ? 'selected' : '' }}>Virement bancaire</option>
            </select>
        </div>

        <button type="submit" class="bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-funnel"></i> Filtrer
        </button>

        @if(request()->hasAny(['search', 'statut', 'moyen_paiement']))
        <a href="{{ route('admin.payouts.index') }}" class="text-agau-muted text-sm hover:text-red-600 transition-colors flex items-center gap-1">
            <i class="bi bi-x-circle"></i> Réinitialiser
        </a>
        @endif
    </form>

    <!-- Tableau -->
    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-6 py-5">Date</th>
                    <th class="px-6 py-5">Auteur</th>
                    <th class="px-6 py-5">Montant demandé</th>
                    <th class="px-6 py-5">Moyen & Coordonnées</th>
                    <th class="px-6 py-5">Statut</th>
                    <th class="px-6 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($payouts as $payout)
                <tr class="hover:bg-agau-blue/5 transition-colors">
                    <td class="px-6 py-4 text-xs text-agau-muted font-lora">
                        {{ $payout->created_at ? $payout->created_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-agau-text">{{ $payout->author?->nom ?? 'Auteur inconnu' }}</div>
                        <div class="text-xs text-agau-muted">
                            Solde actuel : <span class="font-semibold text-emerald-600">{{ number_format($payout->author?->solde_disponible ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-bold text-base text-agau-text">
                        {{ number_format($payout->montant, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-agau-blue/10 text-agau-blue mb-1">
                            {{ match($payout->moyen_paiement) {
                                'tmoney' => 'T-Money',
                                'flooz' => 'Flooz',
                                default => 'Virement Bancaire'
                            } }}
                        </span>
                        <div class="text-xs font-mono text-agau-text select-all">
                            {{ $payout->coordonnees_paiement }}
                        </div>
                        @if($payout->reference_transaction)
                            <div class="text-[11px] text-emerald-700 mt-1">
                                Réf : <span class="font-mono font-bold">{{ $payout->reference_transaction }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $badge = match($payout->statut) {
                                'valide' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'rejete' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                            $label = match($payout->statut) {
                                'valide' => 'Validé & Versé',
                                'rejete' => 'Rejeté',
                                default => 'En attente',
                            };
                        @endphp
                        <span class="{{ $badge }} border px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($payout->statut === 'en_attente')
                        <div class="flex items-center justify-center gap-2">
                            <!-- Bouton Valider avec Modale simple -->
                            <button type="button" onclick="openApproveModal({{ $payout->id }}, '{{ addslashes($payout->author?->nom) }}', {{ $payout->montant }})" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-all flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-check2"></i> Valider
                            </button>
                            <!-- Bouton Rejeter -->
                            <button type="button" onclick="openRejectModal({{ $payout->id }})" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-bold hover:bg-red-200 transition-all flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-x"></i> Rejeter
                            </button>
                        </div>
                        @else
                            <span class="text-xs text-agau-muted italic">Traité le {{ $payout->validated_at ? $payout->validated_at->format('d/m/Y') : '-' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-16 text-center text-agau-muted font-lora italic">
                        Aucune demande de versement enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $payouts->links() }}
    </div>
</div>

<!-- Modale de validation de paiement -->
<div id="approveModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
        <h3 class="font-lora text-xl font-bold text-agau-text">Valider le versement</h3>
        <p class="text-xs text-agau-muted leading-relaxed" id="approveModalDesc">
            Veuillez renseigner la référence de transaction suite au virement ou transfert effectué.
        </p>

        <form id="approveForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-agau-muted mb-1">Référence de Transaction *</label>
                <input type="text" name="reference_transaction" required placeholder="Ex: TXN_987654321 / Ref T-Money" class="w-full px-3 py-2 border border-agau-border/50 rounded-xl text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-agau-muted mb-1">Notes / Justificatif (Optionnel)</label>
                <textarea name="admin_notes" rows="2" placeholder="Informations complémentaires..." class="w-full px-3 py-2 border border-agau-border/50 rounded-xl text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-agau-border/20">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 text-xs font-bold text-agau-muted hover:text-agau-text cursor-pointer">Annuler</button>
                <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-emerald-700 cursor-pointer">Confirmer le paiement</button>
            </div>
        </form>
    </div>
</div>

<!-- Modale de rejet -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
        <h3 class="font-lora text-xl font-bold text-agau-text">Rejeter la demande</h3>
        <p class="text-xs text-agau-muted leading-relaxed">
            Indiquez le motif du rejet (ex: numéro erroné, nom de compte discordant).
        </p>

        <form id="rejectForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-agau-muted mb-1">Motif du rejet *</label>
                <textarea name="admin_notes" required rows="3" placeholder="Motif notifié à l'auteur..." class="w-full px-3 py-2 border border-agau-border/50 rounded-xl text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-agau-border/20">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-xs font-bold text-agau-muted hover:text-agau-text cursor-pointer">Annuler</button>
                <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-red-700 cursor-pointer">Rejeter la demande</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id, authorName, montant) {
        document.getElementById('approveForm').action = '/admin/payouts/' + id + '/approve';
        document.getElementById('approveModalDesc').innerText = 'Validation du versement de ' + Number(montant).toLocaleString('fr-FR') + ' FCFA pour ' + authorName + '.';
        document.getElementById('approveModal').classList.remove('hidden');
    }
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }
    function openRejectModal(id) {
        document.getElementById('rejectForm').action = '/admin/payouts/' + id + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
