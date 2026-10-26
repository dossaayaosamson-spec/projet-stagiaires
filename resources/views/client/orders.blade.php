@extends('layouts.dashboard')

@section('title', 'Mes Commandes — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Historique des <span class="text-agau-blue">Commandes</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Gérez vos achats, suivez vos livraisons et consultez vos détails.</p>
    </div>

    <div class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-[0_4px_20px_rgba(15,74,122,0.03)]">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-agau-off-white border-b border-agau-border">
                    <th class="px-[20px] py-[16px] text-[0.7rem] font-bold uppercase tracking-[1px] text-agau-muted w-[60px]">N°</th>
                    <th class="px-[24px] py-[16px] text-[0.7rem] font-bold uppercase tracking-[1px] text-agau-muted">Ouvrage(s) commandé(s)</th>
                    <th class="px-[20px] py-[16px] text-[0.7rem] font-bold uppercase tracking-[1px] text-agau-muted">Date</th>
                    <th class="px-[20px] py-[16px] text-[0.7rem] font-bold uppercase tracking-[1px] text-agau-muted">Montant</th>
                    <th class="px-[20px] py-[16px] text-[0.7rem] font-bold uppercase tracking-[1px] text-agau-muted">Statut</th>
                    <th class="px-[20px] py-[16px] text-[0.7rem] font-bold uppercase tracking-[1px] text-agau-muted text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                <tr class="border-b border-agau-border/60 hover:bg-agau-off-white/50 transition-colors">
                    <td class="px-[20px] py-[16px] text-[0.85rem] font-bold text-agau-blue">
                        #{{ $loop->iteration }}
                    </td>
                    <td class="px-[24px] py-[16px]">
                        @if($order->items->isNotEmpty())
                            <div class="space-y-1">
                                @foreach($order->items as $item)
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-book text-agau-blue text-[13px]"></i>
                                        <span class="text-[0.85rem] font-semibold text-agau-text">
                                            {{ $item->book?->titre ?? 'Ouvrage AGAU' }}
                                        </span>
                                        @if($item->quantite > 1)
                                            <span class="text-[0.75rem] font-bold text-agau-muted bg-agau-border/30 px-1.5 py-0.5 rounded">x{{ $item->quantite }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-[0.85rem] text-agau-muted italic">Article non spécifié</span>
                        @endif
                    </td>
                    <td class="px-[20px] py-[16px] text-[0.85rem] text-agau-muted font-medium">
                        {{ $order->created_at ? $order->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-[20px] py-[16px] text-[0.85rem] font-bold text-agau-text">
                        {{ number_format($order->total, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-[20px] py-[16px]">
                        @php
                            $statusClasses = match($order->statut) {
                                'confirmée', 'paye', 'livrée' => 'text-[#15803d] bg-[#dcfce7]',
                                'en_route', 'en_preparation' => 'text-[#1d4ed8] bg-[#dbeafe]',
                                'annulée' => 'text-[#b91c1c] bg-[#fee2e2]',
                                default => 'text-[#b45309] bg-[#fef3c7]',
                            };
                            $statusLabels = [
                                'en_attente' => 'En attente',
                                'en_attente_transfert' => 'Attente transfert',
                                'en_attente_verification' => 'Vérification',
                                'confirmée' => 'Confirmée',
                                'en_preparation' => 'En préparation',
                                'en_route' => 'En route',
                                'livrée' => 'Livrée',
                                'annulée' => 'Annulée',
                            ];
                        @endphp
                        <span class="text-[0.7rem] font-bold px-[10px] py-[4px] rounded-full inline-block {{ $statusClasses }}">
                            {{ $statusLabels[$order->statut] ?? ucfirst(str_replace('_', ' ', $order->statut)) }}
                        </span>
                    </td>
                    <td class="px-[20px] py-[16px] text-right">
                        <button onclick="toggleOrderDetails({{ $order->id }})" class="inline-flex items-center gap-1 text-[0.75rem] font-bold text-agau-blue bg-agau-blue/10 hover:bg-agau-blue hover:text-white px-3 py-1.5 rounded-lg transition-all border-none cursor-pointer">
                            <i class="bi bi-eye"></i>
                            <span>Détails</span>
                        </button>
                    </td>
                </tr>

                <!-- Tiroir de détails dépliant -->
                <tr id="order-details-{{ $order->id }}" class="hidden bg-[#f8fafc] border-b border-agau-border">
                    <td colspan="6" class="p-6">
                        <div class="bg-white border border-agau-border/60 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex justify-between items-center border-b border-agau-border/40 pb-3">
                                <div>
                                    <h4 class="text-sm font-bold text-agau-text m-0">Détails de la Commande #{{ $loop->iteration }}</h4>
                                    <p class="text-xs text-agau-muted mt-0.5 m-0">Passée le {{ $order->created_at->translatedFormat('d F Y à H:i') }}</p>
                                </div>
                                <div class="text-xs text-agau-muted font-medium">
                                    Format : <strong class="text-agau-text">{{ $order->isPhysique() ? 'Livre Papier (Livraison)' : 'Numérique / E-book' }}</strong>
                                </div>
                            </div>

                            <!-- Tableau récapitulatif des articles -->
                            <div class="space-y-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-agau-muted block">Articles inclus :</span>
                                <div class="divide-y divide-agau-border/30 border border-agau-border/40 rounded-lg overflow-hidden">
                                    @foreach($order->items as $item)
                                        <div class="p-3 bg-white flex justify-between items-center text-xs">
                                            <div>
                                                <strong class="text-agau-text text-sm">{{ $item->book?->titre ?? 'Ouvrage AGAU' }}</strong>
                                                @if($item->book?->auteur)
                                                    <span class="text-agau-muted block text-[11px]">Par {{ $item->book->auteur }}</span>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="text-agau-muted">Qté: {{ $item->quantite }}</span> • 
                                                <strong class="text-agau-text">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }} FCFA</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if($order->isPhysique())
                                <div class="bg-agau-off-white/80 p-3.5 rounded-lg border border-agau-border/40 text-xs space-y-1">
                                    <strong class="text-agau-blue block font-bold uppercase text-[10px] tracking-wider mb-1">Informations de Livraison :</strong>
                                    <div><strong>Destinataire :</strong> {{ $order->nom_destinataire ?? $order->user?->prenom.' '.$order->user?->nom_famille }}</div>
                                    <div><strong>Adresse :</strong> {{ $order->adresse_livraison ?? 'Non renseignée' }}</div>
                                    <div><strong>Téléphone :</strong> {{ $order->telephone_livraison ?? 'Non renseigné' }}</div>
                                    @if($order->nom_livreur)
                                        <div class="mt-1 pt-1 border-t border-agau-border/40 text-emerald-700">
                                            <i class="bi bi-truck"></i> <strong>Livreur :</strong> {{ $order->nom_livreur }} ({{ $order->telephone_livreur }})
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-[24px] py-[60px] text-center text-[0.9rem] text-agau-muted">
                        <div class="mb-[12px] text-[32px] text-agau-muted/50"><i class="bi bi-cart-x"></i></div>
                        Vous n'avez pas encore effectué de commande.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleOrderDetails(orderId) {
        const drawer = document.getElementById('order-details-' + orderId);
        if (drawer) {
            drawer.classList.toggle('hidden');
        }
    }
</script>
@endsection
