@extends('layouts.admin')

@section('title', 'Modération des commentaires — AGAU Éditions')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-agau-text font-lora">Modération des commentaires</h1>
        <p class="text-sm text-agau-muted mt-1">Gérez les discussions et signalez les contenus problématiques.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.moderation.flagged') }}"
           class="flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold hover:bg-amber-100 transition-all no-underline">
            <i class="bi bi-flag-fill"></i> Signalés
        </a>
        <a href="{{ route('admin.moderation.rapport') }}"
           class="flex items-center gap-2 px-4 py-2 bg-agau-blue/10 text-agau-blue rounded-lg text-xs font-bold hover:bg-agau-blue/20 transition-all no-underline">
            <i class="bi bi-bar-chart-fill"></i> Rapport
        </a>
        {{-- Résolution alias --}}
        <button onclick="document.getElementById('alias-modal').style.display='flex'"
           class="flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-xs font-bold hover:bg-purple-100 transition-all cursor-pointer border-none" style="border:1px solid #e9d5ff;">
            <i class="bi bi-incognito"></i> Résoudre alias
        </button>
    </div>
</div>

{{-- Filtres --}}
<form method="GET" action="{{ route('admin.moderation.index') }}"
      class="bg-white rounded-xl border border-agau-border/30 p-5 mb-8 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="text-xs font-bold text-agau-muted uppercase tracking-wider block mb-1">Contenu</label>
            <input type="text" name="contenu" value="{{ $filters['contenu'] ?? '' }}"
                   placeholder="Rechercher dans le texte..."
                   class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue/50 outline-none">
        </div>
        <div>
            <label class="text-xs font-bold text-agau-muted uppercase tracking-wider block mb-1">Alias</label>
            <input type="text" name="alias" value="{{ $filters['alias'] ?? '' }}"
                   placeholder="Rechercher un alias..."
                   class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue/50 outline-none">
        </div>
        <div>
            <label class="text-xs font-bold text-agau-muted uppercase tracking-wider block mb-1">Statut</label>
            <select name="statut" class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-agau-blue/30 outline-none">
                <option value="">Tous</option>
                <option value="publié" {{ ($filters['statut'] ?? '') === 'publié' ? 'selected' : '' }}>Publiés</option>
                <option value="archivé" {{ ($filters['statut'] ?? '') === 'archivé' ? 'selected' : '' }}>Archivés</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-agau-muted uppercase tracking-wider block mb-1">Signalés</label>
            <select name="is_flagged" class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-agau-blue/30 outline-none">
                <option value="">Tous</option>
                <option value="1" {{ ($filters['is_flagged'] ?? '') === '1' ? 'selected' : '' }}> <i class="bi bi-flag-fill"></i> Signalés uniquement</option>
                <option value="0" {{ ($filters['is_flagged'] ?? '') === '0' ? 'selected' : '' }}><i class="bi bi-flag"></i> Non signalés</option>
            </select>
        </div>
    </div>
    <div class="flex gap-3 mt-4">
        <button type="submit" class="bg-agau-blue text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-agau-blue-dark transition-all">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="{{ route('admin.moderation.index') }}" class="bg-agau-border/20 text-agau-muted px-5 py-2 rounded-lg text-xs font-bold hover:bg-agau-border/30 transition-all no-underline">
            Réinitialiser
        </a>
    </div>
</form>

{{-- Tableau des commentaires --}}
<div class="bg-white rounded-xl border border-agau-border/30 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-agau-border/20 bg-agau-off-white">
                    <th class="text-left px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">Auteur / Alias</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">Contenu</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">Contexte</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">Statut</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">Date</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-agau-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-agau-border/10">
                @forelse($commentaires as $commentaire)
                    <tr class="hover:bg-agau-blue/2 transition-colors {{ $commentaire->is_flagged ? 'bg-amber-50/50' : '' }}">
                        <td class="px-6 py-4 text-xs text-agau-muted font-mono">{{ $commentaire->id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-agau-text">{{ $commentaire->display_name }}</div>
                            @if($commentaire->alias)
                                <div class="text-xs text-agau-muted">
                                    <i class="bi bi-incognito"></i>
                                    Alias de : {{ $commentaire->user->nom_complet }}
                                </div>
                            @else
                                <div class="text-xs text-agau-muted">{{ $commentaire->user->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <p class="text-sm text-agau-text truncate">{{ $commentaire->contenu }}</p>
                            @if($commentaire->is_flagged)
                                <span class="inline-flex items-center gap-1 text-xs bg-amber-100 text-amber-800 rounded-full px-2 py-0.5 mt-1 font-semibold">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $commentaire->flag_reason }}
                                </span>
                            @endif
                            @if($commentaire->parent_id)
                                <span class="text-xs text-agau-muted block mt-1"><i class="bi bi-reply-fill"></i> Réponse à #{{ $commentaire->parent_id }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-agau-muted">
                            @if($commentaire->commentable_type)
                                @php
                                    $typeLabel = class_basename($commentaire->commentable_type);
                                    $oeuvre = $commentaire->commentable;
                                @endphp
                                <span class="font-semibold">{{ $typeLabel }}</span><br>
                                {{ $oeuvre?->titre ?? 'Œuvre supprimée' }}
                            @else
                                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">Blog général</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($commentaire->statut === 'archivé')
                                <span class="inline-flex items-center gap-1 text-xs bg-red-50 text-red-700 rounded-full px-3 py-1 font-semibold">
                                    <i class="bi bi-archive-fill"></i> Archivé
                                </span>
                                @if($commentaire->archive_reason)
                                    <div class="text-xs text-agau-muted mt-1 italic">{{ Str::limit($commentaire->archive_reason, 50) }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 text-xs bg-emerald-50 text-emerald-700 rounded-full px-3 py-1 font-semibold">
                                    <i class="bi bi-check-circle-fill"></i> Publié
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-agau-muted">
                            {{ $commentaire->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($commentaire->statut === 'publié')
                                <button
                                    onclick="openArchiveModal({{ $commentaire->id }}, '{{ addslashes(Str::limit($commentaire->contenu, 60)) }}')"
                                    class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-semibold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all border-none cursor-pointer"
                                >
                                    <i class="bi bi-archive-fill"></i> Archiver
                                </button>
                            @else
                                <span class="text-xs text-agau-muted italic">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-agau-muted">
                            <i class="bi bi-chat-dots text-4xl block mb-3 opacity-30"></i>
                            Aucun commentaire trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($commentaires->hasPages())
        <div class="px-6 py-4 border-t border-agau-border/20">
            {{ $commentaires->links() }}
        </div>
    @endif
</div>

{{-- Modal archivage --}}
<div id="archive-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="font-bold text-lg text-red-600 mb-2"><i class="bi bi-archive-fill"></i> Archiver le commentaire</h3>
        <p class="text-sm text-agau-muted mb-1">Commentaire :</p>
        <p id="archive-modal-excerpt" class="text-sm italic text-agau-text mb-5 bg-agau-off-white px-3 py-2 rounded-lg"></p>
        <form id="archive-form" method="POST">
            @csrf
            <label class="text-xs font-bold text-agau-muted uppercase tracking-wider block mb-2">Motif *</label>
            <textarea name="motif" rows="3" required placeholder="Décrivez le motif de cet archivage..."
                class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-sm mb-4 outline-none focus:ring-1 focus:ring-agau-blue/30 resize-none"></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white border-none rounded-lg py-2.5 text-sm font-bold cursor-pointer hover:bg-red-700 transition-all">
                    Confirmer
                </button>
                <button type="button" onclick="document.getElementById('archive-modal').style.display='none'"
                    class="flex-1 bg-agau-off-white text-agau-muted border-none rounded-lg py-2.5 text-sm font-bold cursor-pointer hover:bg-agau-border/20 transition-all">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal résolution alias --}}
<div id="alias-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="font-bold text-lg text-purple-700 mb-2"><i class="bi bi-incognito"></i> Résoudre un alias</h3>
        <p class="text-xs text-agau-muted mb-4">Cette action est journalisée. L'identité réelle ne sera jamais divulguée publiquement.</p>
        <div class="flex gap-2 mb-4">
            <input id="alias-input" type="text" placeholder="Entrez l'alias à résoudre..."
                class="flex-1 border border-agau-border/40 rounded-lg px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-purple-400">
            <button onclick="resolveAlias()" class="bg-purple-600 text-white border-none rounded-lg px-4 py-2 text-sm font-bold cursor-pointer hover:bg-purple-700 transition-all">
                Résoudre
            </button>
        </div>
        <div id="alias-result" class="hidden bg-purple-50 border border-purple-200 rounded-lg p-4 text-sm"></div>
        <button onclick="document.getElementById('alias-modal').style.display='none'"
            class="w-full mt-4 bg-agau-off-white text-agau-muted border-none rounded-lg py-2 text-sm font-bold cursor-pointer hover:bg-agau-border/20 transition-all">
            Fermer
        </button>
    </div>
</div>

@push('scripts')
<script>
function openArchiveModal(id, excerpt) {
    document.getElementById('archive-form').action = '/admin/moderation/' + id + '/archiver';
    document.getElementById('archive-modal-excerpt').textContent = '"' + excerpt + '..."';
    document.getElementById('archive-modal').style.display = 'flex';
}

function resolveAlias() {
    const alias = document.getElementById('alias-input').value.trim();
    if (!alias) return;

    fetch('{{ route('admin.moderation.resolve-alias') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: JSON.stringify({ alias }),
    })
    .then(r => r.json())
    .then(data => {
        const result = document.getElementById('alias-result');
        result.classList.remove('hidden');
        if (data.found) {
            result.innerHTML = `
                <p class="font-bold text-purple-800 mb-2"><i class="bi bi-check-circle"></i> Identité résolue :</p>
                <p><strong>Alias :</strong> ${data.alias}</p>
                <p><strong>Nom :</strong> ${data.nom_complet}</p>
                <p><strong>Email :</strong> ${data.email}</p>
                <p><strong>Membre depuis :</strong> ${data.membre_depuis}</p>
                <p class="text-xs text-agau-muted mt-2 italic"><i class="bi bi-exclamation-triangle-fill"></i> Information strictement confidentielle — action journalisée.</p>
            `;
        } else {
            result.innerHTML = `<p class="text-red-600">${data.message}</p>`;
        }
    });
}
</script>
@endpush
@endsection
