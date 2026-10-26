@extends('layouts.admin')

@section('title', 'Détails Commande #' . $order->id . ' — Admin AGAU')
@section('header-title', 'Expertise Transactionnelle')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <i class="bi bi-arrow-left text-[18px] group-hover:-translate-x-1 transition-transform"></i>
            Retour au Flux
        </a>
        
        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-agau-blue text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-[#1B5E8A] transition-all no-underline shadow-sm hover:shadow">
            <i class="bi bi-file-earmark-pdf text-[18px]"></i>
            Télécharger Facture PDF
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-8 border-b border-agau-border/20 bg-agau-border/5 flex justify-between items-start">
                    <div>
                        <h2 class="font-lora text-2xl font-bold text-agau-text">Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-xs text-agau-muted font-medium mt-1">Passée le {{ $order->created_at ? $order->created_at->translatedFormat('d F Y à H:i') : '-' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-2">Statut Actuel</span>
                        @php
                            $statusClasses = [
                                'en_attente' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'en_attente_transfert' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'en_attente_verification' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'confirmée' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'livrée' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'annulée' => 'bg-red-50 text-red-700 border-red-100'
                            ];
                            $currentClass = $statusClasses[$order->statut] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                        @endphp
                        <span class="{{ $currentClass }} px-4 py-1.5 border rounded-full text-[10px] font-bold uppercase tracking-widest shadow-sm">
                            {{ str_replace('_', ' ', $order->statut) }}
                        </span>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <section>
                        <h3 class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="bi bi-bag-check text-[18px]"></i>
                            Articles commandés
                        </h3>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                            <div class="flex items-center justify-between p-4 bg-agau-border/5 rounded-xl border border-agau-border/10 group hover:border-agau-blue/20 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-18 bg-agau-off-white rounded-lg shrink-0 overflow-hidden shadow-sm">
                                        @if($item->book && $item->book->cover_path)
                                            <img src="{{ asset('storage/' . $item->book->cover_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-agau-blue/5">
                                                <i class="bi bi-book text-agau-blue/20"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-agau-text">{{ $item->book->titre ?? 'Livre archivé' }}</p>
                                        <p class="text-[10px] text-agau-muted font-medium mt-0.5">
                                            Qté: {{ $item->quantite }} × <span class="text-agau-blue font-bold">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-agau-text">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- Section livraison : visible uniquement pour les commandes physiques --}}
                    @if($order->type_commande === 'physique')
                    <section class="pt-6 border-t border-agau-border/20">
                        <h3 class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="bi bi-truck text-[18px] text-amber-600"></i>
                            Informations de livraison
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5 bg-amber-50/40 border border-amber-100 rounded-xl">
                            <div>
                                <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Destinataire</span>
                                <span class="text-sm font-bold text-agau-text">{{ $order->nom_destinataire ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Téléphone</span>
                                <span class="text-sm font-bold text-agau-text">{{ $order->telephone_livraison ?? '—' }}</span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Adresse</span>
                                <span class="text-sm text-agau-text">{{ $order->adresse_livraison ?? '—' }}, {{ $order->ville_livraison }} — {{ $order->pays_livraison }}</span>
                            </div>
                            @if($order->notes_livraison)
                            <div class="md:col-span-2">
                                <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Instructions</span>
                                <span class="text-sm text-agau-text italic">{{ $order->notes_livraison }}</span>
                            </div>
                            @endif
                            @if($order->frais_livraison)
                            <div>
                                <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Frais de livraison</span>
                                <span class="text-sm font-black text-amber-700">{{ number_format($order->frais_livraison, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @endif
                            @if($order->nom_livreur)
                            <div>
                                <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Livreur</span>
                                <span class="text-sm font-bold text-agau-text">{{ $order->nom_livreur }} — {{ $order->telephone_livreur }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Formulaire d'expédition : visible si commande confirmée et pas encore en_route --}}
                        @if(in_array($order->statut, ['confirmée', 'en_preparation']) && $order->statut !== 'en_route')
                        <div class="mt-6 p-5 bg-white border border-agau-border/40 rounded-xl space-y-4">
                            <h4 class="text-[10px] font-bold text-agau-muted uppercase tracking-widest flex items-center gap-2">
                                <i class="bi bi-box-seam text-[16px] text-agau-blue"></i>
                                Expédier la commande
                            </h4>
                            <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Nom du livreur <span class="text-red-500">*</span></label>
                                        <input type="text" name="nom_livreur" value="{{ old('nom_livreur') }}" required
                                            class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-xs text-agau-text focus:outline-none focus:border-agau-blue">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Tél. livreur <span class="text-red-500">*</span></label>
                                        <input type="tel" name="telephone_livreur" value="{{ old('telephone_livreur') }}" required
                                            class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-xs text-agau-text focus:outline-none focus:border-agau-blue">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-1">Frais de livraison (min. 500 FCFA) <span class="text-red-500">*</span></label>
                                        <input type="number" name="frais_livraison" value="{{ old('frais_livraison', 500) }}" min="500" step="100" required
                                            class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-xs text-agau-text focus:outline-none focus:border-agau-blue">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none flex items-center justify-center gap-2"
                                    onclick="return confirm('Confirmer l\'expédition ? Un e-mail sera envoyé au client avec les coordonnées du livreur.')">
                                    <i class="bi bi-truck"></i>
                                    Expédier & Notifier le client
                                </button>
                            </form>
                        </div>
                        @endif
                    </section>
                    @endif

                    <section class="flex justify-end pt-8 border-t border-agau-border/20">
                        <div class="w-64 space-y-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-agau-muted font-bold uppercase tracking-tighter">Sous-total livres</span>
                                <span class="text-agau-text font-bold">{{ number_format($order->items->sum(fn($i) => $i->prix_unitaire * $i->quantite), 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($order->frais_livraison)
                            <div class="flex justify-between text-xs">
                                <span class="text-agau-muted font-bold uppercase tracking-tighter">Frais de livraison</span>
                                <span class="text-amber-700 font-bold">{{ number_format($order->frais_livraison, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @elseif($order->type_commande !== 'physique')
                            <div class="flex justify-between text-xs">
                                <span class="text-agau-muted font-bold uppercase tracking-tighter">Logistique</span>
                                <span class="text-emerald-600 font-bold uppercase tracking-widest text-[9px]">OFFERT</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center pt-4 border-t border-agau-border/30">
                                <span class="text-xs font-bold text-agau-text uppercase tracking-widest">Total</span>
                                <span class="text-xl font-black text-agau-blue">{{ number_format($order->total, 0, ',', ' ') }} <span class="text-xs">FCFA</span></span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Modalité & Preuve de Paiement Section -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm p-8 space-y-6">
                @if($order->transaction_proof || $order->payment_method === 'transfert_manuel')
                    <!-- Paiement Manuel avec Reçu -->
                    <h3 class="text-[11px] font-bold text-agau-muted uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-receipt text-[18px]"></i>
                        Paiement Manuel — Reçu de Transfert
                    </h3>

                    @if($order->transaction_proof)
                        <div class="p-5 bg-amber-50/60 border border-amber-200/80 rounded-xl space-y-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="bi bi-file-earmark-image text-3xl text-amber-600"></i>
                                    <div>
                                        <p class="text-xs font-bold text-agau-text">Reçu fourni par le client</p>
                                        <p class="text-[10px] text-agau-muted">Fichier téléversé pour validation manuelle</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $order->transaction_proof) }}" target="_blank" class="px-3.5 py-2 bg-white border border-agau-border/40 rounded-lg text-xs font-bold text-agau-blue hover:bg-agau-blue hover:text-white transition-all no-underline inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    Voir / Télécharger
                                </a>
                            </div>

                            <div class="overflow-hidden rounded-lg border border-agau-border/20 bg-black/5 max-h-96 flex justify-center items-center">
                                <img src="{{ asset('storage/' . $order->transaction_proof) }}" alt="Preuve de paiement" class="max-h-96 object-contain rounded">
                            </div>

                            <!-- Rapid Actions for Manual Proof -->
                            <div class="pt-3 border-t border-amber-200/50 flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('admin.orders.approve', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer border-none flex items-center gap-2">
                                        <i class="bi bi-check-circle"></i>
                                        Valider le paiement & Confirmer la commande
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="inline" onsubmit="return confirm('Rejeter cette preuve et annuler la commande ?')">
                                    @csrf
                                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer border-none flex items-center gap-2">
                                        <i class="bi bi-x-circle"></i>
                                        Rejeter la preuve & Annuler
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="p-6 bg-amber-50/40 rounded-xl text-center space-y-2 border border-dashed border-amber-200">
                            <i class="bi bi-clock-history text-amber-600 text-3xl"></i>
                            <p class="text-xs text-agau-text font-bold">En attente de transfert / reçu</p>
                            <p class="text-[11px] text-agau-muted">Le client a choisi le paiement manuel mais n'a pas encore téléversé la capture d'écran du reçu.</p>
                        </div>
                    @endif
                @else
                    <!-- Paiement Automatique FedaPay -->
                    <h3 class="text-[11px] font-bold text-agau-muted uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-credit-card-2-front text-[18px] text-agau-blue"></i>
                        Paiement en Ligne — Passerelle FedaPay
                    </h3>

                    <div class="p-5 bg-blue-50/40 border border-blue-100 rounded-xl space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-xs font-bold text-agau-text">Flux de paiement 100% Automatique (FedaPay Mobile Money / CB)</p>
                        </div>
                        <p class="text-[11px] text-agau-muted leading-relaxed">
                            Cette commande utilise le système de paiement direct FedaPay. 
                            Aucun reçu de paiement physique ou téléversement n'est nécessaire de la part du client. 
                            Dès que le règlement est validé par FedaPay (Mobile Money T-Money / Moov Money / Carte), la commande bascule <strong>automatiquement</strong> au statut <strong>Confirmée / Livrée</strong> via le système de Webhook FedaPay.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-8">
            <!-- Client Info -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm p-6 space-y-6">
                <h3 class="text-[10px] font-bold text-agau-muted uppercase tracking-widest flex items-center gap-2">
                    <i class="bi bi-person-circle text-[18px]"></i>
                    Acquéreur
                </h3>
                
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-agau-blue/10 flex items-center justify-center text-agau-blue font-bold shadow-inner">
                        {{ strtoupper(substr($order->user?->prenom ?? 'C', 0, 1) . substr($order->user?->nom_famille ?? 'I', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-agau-text leading-tight">{{ $order->user?->nom_complet ?? 'Client inconnu' }}</p>
                        <p class="text-[10px] text-agau-muted font-medium mt-1 uppercase tracking-tighter">Client AGAU.ED</p>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-agau-border/10">
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block">E-mail</span>
                        <span class="text-xs text-agau-text font-bold break-all">{{ $order->user?->email ?? 'N/A' }}</span>
                    </div>
                    @if($order->user?->telephone)
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block">Téléphone</span>
                        <span class="text-xs text-agau-text font-bold">{{ $order->user->telephone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Changement Manuel de Statut -->
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm p-6 space-y-6">
                <h3 class="text-[10px] font-bold text-agau-muted uppercase tracking-widest flex items-center gap-2">
                    <i class="bi bi-sliders text-[18px]"></i>
                    Gestion du Statut
                </h3>

                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest block mb-2">Changer le statut :</label>
                        <select name="statut" class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-2.5 text-xs font-bold text-agau-text focus:ring-2 focus:ring-agau-blue/20 focus:outline-none">
                            <option value="en_attente" {{ $order->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_attente_transfert" {{ $order->statut === 'en_attente_transfert' ? 'selected' : '' }}>En attente de transfert (Manuel)</option>
                            <option value="en_attente_verification" {{ $order->statut === 'en_attente_verification' ? 'selected' : '' }}>En attente de vérification (Reçu manuel)</option>
                            <option value="confirmée" {{ $order->statut === 'confirmée' ? 'selected' : '' }}>Confirmée</option>
                            <option value="en_preparation" {{ $order->statut === 'en_preparation' ? 'selected' : '' }}>En préparation (physique)</option>
                            <option value="en_route" {{ $order->statut === 'en_route' ? 'selected' : '' }}>En route (physique)</option>
                            <option value="livrée" {{ $order->statut === 'livrée' ? 'selected' : '' }}>Livrée</option>
                            <option value="annulée" {{ $order->statut === 'annulée' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-agau-blue hover:bg-agau-blue-dark text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer border-none">
                        Enregistrer le statut
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
