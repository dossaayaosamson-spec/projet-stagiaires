@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 md:px-8 py-8 md:py-12 max-w-7xl mx-auto">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="font-serif text-2xl md:text-3xl font-bold" style="color:#0D3B64;">Bibliothèque d'auteurs</h1>
            <p class="text-sm mt-1" style="color:#64748B;">Gérez les auteurs de référence pour l'apprentissage stylistique</p>
        </div>
        <button onclick="openAuthorModal()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all hover:opacity-90" style="background:#1D6FB8; color:#fff;">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 2v12M2 8h12" stroke="white" stroke-width="1.5"/></svg>
            Nouvel auteur
        </button>
    </div>

    {{-- Grille des auteurs --}}
    @if($authors->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($authors as $author)
        <div class="bg-white rounded-2xl p-5 md:p-6 hover-lift transition-all duration-300" style="border:1px solid #E2E8F0;">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-serif text-lg md:text-xl font-semibold" style="color:#0D3B64;">{{ $author->name ?? 'Nom inconnu' }}</h3>
                    <!-- <p class="text-xs mt-1" style="color:#94A3B8;">{{ $author->manuscripts_count ?? 0 }} manuscrits</p> -->
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('manuscripts.infoauteur', $author->id) }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Voir détails">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M11 2l3 3-7 7-3 1 1-3 7-7z" stroke="currentColor" stroke-width="1.2"/><path d="M9 4l3 3" stroke="currentColor" stroke-width="1.2"/></svg>
                    </a>
                </div>
            </div>
            <p class="text-sm" style="color:#475569;">{{ Str::limit($author->style_description ?? 'Aucune description', 100) }}</p>
            @if(!empty($author->typical_themes))
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach(array_slice($author->typical_themes, 0, 3) as $theme)
                <span class="text-[10px] px-2 py-1 rounded-full" style="background:#EEF5FD; color:#1D6FB8;">#{{ $theme }}</span>
                @endforeach
                @if(count($author->typical_themes) > 3)
                <span class="text-[10px] px-2 py-1 rounded-full bg-gray-100 text-gray-500">+{{ count($author->typical_themes) - 3 }}</span>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl p-8 md:p-12 text-center" style="border:1px solid #E2E8F0;">
        <p class="text-gray-500">Aucun auteur enregistré pour le moment.</p>
        <button onclick="openAuthorModal()" class="mt-4 px-4 py-2 rounded-xl text-sm transition-all hover:opacity-90" style="background:#1D6FB8; color:#fff;">
            Créer mon premier auteur
        </button>
    </div>
    @endif
</div>

{{-- MODAL NOUVEL AUTEUR --}}
<div id="authorModal" class="fixed top-16 inset-0 z-50 hidden overflow-y-auto" style="background:rgba(0,0,0,0.5);">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-5 md:p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-serif text-xl font-semibold" style="color:#0D3B64;">Nouvel auteur</h3>
                <button onclick="closeAuthorModal()" class="p-1 text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form id="authorForm" onsubmit="saveAuthor(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Nom de l'auteur</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border-color:#E2E8F0;">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Biographie</label>
                        <textarea name="biography" rows="3" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border-color:#E2E8F0;"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Description du style</label>
                        <textarea name="style_description" rows="3" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border-color:#E2E8F0;"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Thèmes récurrents</label>
                        <input type="text" name="typical_themes" placeholder="Séparés par des virgules" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border-color:#E2E8F0;">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Œuvres célèbres</label>
                        <input type="text" name="famous_works" placeholder="Séparés par des virgules" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all" style="border-color:#E2E8F0;">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button type="button" onclick="closeAuthorModal()" class="flex-1 px-4 py-2.5 rounded-xl border hover:bg-gray-50 transition-all" style="border-color:#E2E8F0;">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl transition-all hover:opacity-90" style="background:#1D6FB8; color:#fff;">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const djangoApiUrl = '{{ env("DJANGO_API_URL", "https://agaueditions-ia.saeicubetech.com") }}';

function openAuthorModal() { document.getElementById('authorModal').classList.remove('hidden'); }
function closeAuthorModal() { document.getElementById('authorModal').classList.add('hidden'); }

function saveAuthor(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    fetch(djangoApiUrl + '/api/authors/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: formData.get('name'),
            biography: formData.get('biography'),
            style_description: formData.get('style_description'),
            typical_themes: formData.get('typical_themes') ? formData.get('typical_themes').split(',').map(s => s.trim()) : [],
            famous_works: formData.get('famous_works') ? formData.get('famous_works').split(',').map(s => s.trim()) : []
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + (data.error || 'Impossible de créer l\'auteur'));
        }
    })
    .catch(error => { 
        console.error('Erreur:', error);
        alert('Erreur: ' + error.message); 
    });
}
</script>
@endsection