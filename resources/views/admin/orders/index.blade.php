@extends('layouts.admin')

@section('title', 'Flux des Ventes — Admin AGAU Éditions')
@section('header-title', 'Tableau des Transactions')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Commandes</h1>
            <p class="text-sm text-agau-muted font-medium">Suivi et gestion du flux transactionnel</p>
        </div>
        <div class="flex items-center gap-3 px-4 py-2 bg-amber-50 border border-amber-100 rounded-lg">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-widest">Flux en temps réel</span>
        </div>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-8 py-5">Référence</th>
                    <th class="px-8 py-5">Client</th>
                    <th class="px-8 py-5">Date</th>
                    <th class="px-8 py-5">Montant</th>
                    <th class="px-8 py-5">État & Reçu</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($orders as $order)
                    <tr class="hover:bg-agau-blue/5 transition-colors group">
                        <td class="px-8 py-5">
                            <span class="font-bold text-agau-blue tracking-tighter">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">{{ $order->user?->nom_complet ?? 'Client inconnu' }}</span>
                                <span class="text-[10px] text-agau-muted font-medium italic">{{ $order->user?->email ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-agau-muted text-xs font-medium">
                            {{ $order->created_at ? $order->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-8 py-5 font-lora font-bold text-agau-blue-dark">
                            {{ number_format($order->total ?? 0, 0, ',', ' ') }} <span class="text-[10px] font-sans">FCFA</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col gap-1 items-start">
                                @if($order->statut === 'en_attente_verification')
                                    <span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight flex items-center gap-1 border border-amber-300">
                                        <i class="bi bi-exclamation-diamond-fill"></i>
                                        À vérifier (Reçu téléversé)
                                    </span>
                                @elseif(in_array($order->statut, ['en_attente', 'en_attente_transfert']))
                                    <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight flex items-center gap-1 border border-amber-200/50">
                                        <i class="bi bi-clock-history"></i>
                                        {{ str_replace('_', ' ', $order->statut) }}
                                    </span>
                                @elseif($order->statut === 'confirmée')
                                    <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight flex items-center gap-1 border border-blue-200/50">
                                        <i class="bi bi-check-circle"></i>
                                        Confirmée
                                    </span>
                                @elseif(in_array($order->statut, ['livrée', 'paye', 'payée']))
                                    <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight flex items-center gap-1 border border-emerald-200/50">
                                        <i class="bi bi-truck"></i>
                                        Livrée
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight flex items-center gap-1">
                                        <i class="bi bi-x-circle"></i>
                                        {{ str_replace('_', ' ', ucfirst($order->statut ?? 'Annulée')) }}
                                    </span>
                                @endif

                                @if($order->transaction_proof)
                                    <a href="{{ asset('storage/' . $order->transaction_proof) }}" target="_blank" class="text-[9px] font-bold text-indigo-600 hover:text-indigo-800 underline flex items-center gap-1 mt-0.5">
                                        <i class="bi bi-paperclip"></i> Voir le reçu
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex gap-3 justify-center items-center">
                                <a href="{{ route('admin.orders.show', $order) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Détails & Validation">
                                    <i class="bi bi-eye" style="font-size: 1.3rem"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Archiver définitivement cette transaction ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer" title="Supprimer">
                                        <i class="bi bi-trash" style="font-size: 1.3rem"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <i class="bi bi-cart-x text-agau-muted" style="font-size: 1.3rem"></i>
                                <p class="text-agau-muted font-lora italic">Aucun flux commercial n'a été détecté.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
