@extends('layouts.admin')

@section('title', 'Commentaires signalés — AGAU Éditions')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-agau-text font-lora">Commentaires signalés</h1>
        <p class="text-sm text-agau-muted mt-1">Commentaires détectés automatiquement comme potentiellement problématiques.</p>
    </div>
    <a href="{{ route('admin.moderation.index') }}"
       class="flex items-center gap-2 px-4 py-2 bg-agau-border/20 text-agau-muted rounded-lg text-xs font-bold hover:bg-agau-border/30 transition-all no-underline">
        <i class="bi bi-arrow-left"></i> Retour modération
    </a>
</div>

@if($commentaires->isEmpty())
    <div class="bg-white rounded-xl border border-agau-border/30 shadow-sm p-16 text-center">
        <i class="bi bi-shield-check text-5xl text-emerald-400 block mb-4"></i>
        <h3 class="font-bold text-lg text-agau-text mb-2">Aucun commentaire signalé</h3>
        <p class="text-agau-muted text-sm">Tout semble en ordre. Aucun contenu problématique détecté.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($commentaires as $commentaire)
            <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        {{-- Priorité --}}
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-amber-100 text-amber-800 rounded-full px-3 py-1">
                                <i class="bi bi-exclamation-triangle-fill"></i> À surveiller
                            </span>
                            <span class="text-xs text-agau-muted">#{{ $commentaire->id }}</span>
                            <span class="text-xs text-agau-muted">{{ $commentaire->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        {{-- Auteur --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-full bg-agau-blue flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($commentaire->display_name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-agau-text">{{ $commentaire->display_name }}</span>
                                @if($commentaire->alias)
                                    <span class="text-xs text-agau-muted ml-2">({{ $commentaire->user->nom_complet }}, {{ $commentaire->user->email }})</span>
                                @else
                                    <span class="text-xs text-agau-muted ml-2">{{ $commentaire->user->email }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Motif du flag --}}
                        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-2 mb-3">
                            <p class="text-xs font-bold text-amber-800 mb-1">Motif du signalement :</p>
                            <p class="text-xs text-amber-700">{{ $commentaire->flag_reason }}</p>
                        </div>

                        {{-- Contenu --}}
                        <div class="bg-agau-off-white rounded-lg px-4 py-3 mb-3">
                            <p class="text-sm text-agau-text whitespace-pre-wrap">{{ $commentaire->contenu }}</p>
                        </div>

                        {{-- Contexte --}}
                        <div class="text-xs text-agau-muted">
                            <i class="bi bi-link-45deg"></i>
                            @if($commentaire->commentable_type)
                                {{ class_basename($commentaire->commentable_type) }} : {{ $commentaire->commentable?->titre ?? 'Œuvre supprimée' }}
                            @else
                                Blog général
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-2 min-w-[140px]">
                        <button
                            onclick="openArchiveModal({{ $commentaire->id }}, '{{ addslashes(Str::limit($commentaire->contenu, 60)) }}')"
                            class="flex items-center justify-center gap-1 text-xs text-white bg-red-600 hover:bg-red-700 border-none rounded-lg px-4 py-2.5 font-bold cursor-pointer transition-all w-full"
                        >
                            <i class="bi bi-archive-fill"></i> Archiver
                        </button>
                        <a href="{{ route('admin.users.show', $commentaire->user_id) }}"
                           class="flex items-center justify-center gap-1 text-xs text-agau-muted hover:text-agau-blue border border-agau-border/40 rounded-lg px-4 py-2.5 font-bold no-underline transition-all">
                            <i class="bi bi-person-fill"></i> Voir profil
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($commentaires->hasPages())
        <div class="mt-6">
            {{ $commentaires->links() }}
        </div>
    @endif
@endif

{{-- Modal archivage --}}
<div id="archive-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="font-bold text-lg text-red-600 mb-2"><i class="bi bi-archive-fill"></i> Archiver le commentaire</h3>
        <p id="archive-modal-excerpt" class="text-sm italic text-agau-text mb-5 bg-agau-off-white px-3 py-2 rounded-lg"></p>
        <form id="archive-form" method="POST">
            @csrf
            <textarea name="motif" rows="3" required placeholder="Motif de l'archivage..."
                class="w-full border border-agau-border/40 rounded-lg px-3 py-2 text-sm mb-4 outline-none resize-none"></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white border-none rounded-lg py-2.5 text-sm font-bold cursor-pointer hover:bg-red-700 transition-all">
                    Confirmer
                </button>
                <button type="button" onclick="document.getElementById('archive-modal').style.display='none'"
                    class="flex-1 bg-agau-off-white text-agau-muted border-none rounded-lg py-2.5 text-sm font-bold cursor-pointer">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openArchiveModal(id, excerpt) {
    document.getElementById('archive-form').action = '/admin/moderation/' + id + '/archiver';
    document.getElementById('archive-modal-excerpt').textContent = '"' + excerpt + '..."';
    document.getElementById('archive-modal').style.display = 'flex';
}
</script>
@endpush
@endsection
