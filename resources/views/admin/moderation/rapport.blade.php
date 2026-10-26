@extends('layouts.admin')

@section('title', 'Rapport des discussions — AGAU Éditions')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-agau-text font-lora">Rapport des discussions</h1>
        <p class="text-sm text-agau-muted mt-1">Vue d'ensemble de l'activité des commentaires sur la plateforme.</p>
    </div>
    <a href="{{ route('admin.moderation.index') }}"
       class="flex items-center gap-2 px-4 py-2 bg-agau-border/20 text-agau-muted rounded-lg text-xs font-bold hover:bg-agau-border/30 transition-all no-underline">
        <i class="bi bi-arrow-left"></i> Retour modération
    </a>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-agau-border/30 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-agau-blue mb-1">{{ $rapport['total_commentaires'] }}</div>
        <div class="text-xs text-agau-muted font-semibold uppercase tracking-wider"><i class="bi bi-chat-square-dots"></i> Commentaies publiés</div>
    </div>
    <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-amber-600 mb-1">{{ $rapport['flagges'] }}</div>
        <div class="text-xs text-agau-muted font-semibold uppercase tracking-wider"><i class="bi bi-exclamation-triangle-fill"></i> Signalés</div>
    </div>
    <div class="bg-white rounded-xl border border-agau-border/30 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-emerald-600 mb-1">{{ $rapport['blog_general'] }}</div>
        <div class="text-xs text-agau-muted font-semibold uppercase tracking-wider">Blog général</div>
    </div>
    <div class="bg-white rounded-xl border border-agau-border/30 shadow-sm p-5 text-center">
        <div class="text-3xl font-bold text-agau-text mb-1">{{ $rapport['top_oeuvres']->count() }}</div>
        <div class="text-xs text-agau-muted font-semibold uppercase tracking-wider">Œuvres commentées</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Top œuvres commentées --}}
    <div class="bg-white rounded-xl border border-agau-border/30 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-agau-border/20">
            <h2 class="font-bold text-sm text-agau-text"><i class="bi bi-fire text-orange-500"></i> Sujets les plus animés</h2>
        </div>
        <div class="divide-y divide-agau-border/10">
            @forelse($rapport['top_oeuvres'] as $i => $item)
                <div class="px-6 py-4 flex items-center gap-4">
                    <span class="text-xl font-bold text-agau-muted/40 w-6">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-agau-text truncate">
                            {{ $item->commentable?->titre ?? 'Œuvre supprimée' }}
                        </div>
                        <div class="text-xs text-agau-muted">{{ class_basename($item->commentable_type) }}</div>
                    </div>
                    <span class="text-sm font-bold text-agau-blue">{{ $item->total }} <span class="text-xs font-normal text-agau-muted">cmts</span></span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-agau-muted text-sm">Aucune donnée.</div>
            @endforelse
        </div>
    </div>

    {{-- Top utilisateurs actifs --}}
    <div class="bg-white rounded-xl border border-agau-border/30 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-agau-border/20">
            <h2 class="font-bold text-sm text-agau-text"><i class="bi bi-people-fill text-agau-blue"></i> Utilisateurs les plus actifs</h2>
        </div>
        <div class="divide-y divide-agau-border/10">
            @forelse($rapport['top_utilisateurs'] as $i => $item)
                <div class="px-6 py-4 flex items-center gap-4">
                    <span class="text-xl font-bold text-agau-muted/40 w-6">{{ $i + 1 }}</span>
                    <div class="w-8 h-8 rounded-full bg-agau-blue flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($item->user?->prenom ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-agau-text truncate">{{ $item->user?->nom_complet ?? 'Compte supprimé' }}</div>
                        <div class="text-xs text-agau-muted truncate">{{ $item->user?->email }}</div>
                    </div>
                    <span class="text-sm font-bold text-agau-blue">{{ $item->total }} <span class="text-xs font-normal text-agau-muted">cmts</span></span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-agau-muted text-sm">Aucune donnée.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
