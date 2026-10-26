@extends('layouts.candidature')

@section('title', 'Étape 2 — Votre œuvre · ECLA')

@section('candidature_content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold text-lg">2</span>
        <h2 class="text-2xl font-extrabold text-gray-900 font-montserrat">Informations sur votre œuvre</h2>
    </div>
    <p class="text-gray-500 font-lora">Décrivez le manuscrit que vous souhaitez soumettre au concours eCLA 2026.</p>
</div>

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex gap-3">
        <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl mt-0.5 flex-shrink-0"></i>
        <ul class="text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('candidature.step2') }}" method="POST" class="space-y-6" id="step2-form" novalidate>
    @csrf

    <div>
        <label for="titre_manuscrit" class="block text-sm font-semibold text-gray-700 mb-1.5">
            <i class="bi bi-type-h1 mr-1 text-indigo-500"></i> Titre du manuscrit <span class="text-red-500">*</span>
        </label>
        <input type="text" id="titre_manuscrit" name="titre_manuscrit"
            value="{{ old('titre_manuscrit', $data['titre_manuscrit'] ?? '') }}"
            class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-indigo-400 @error('titre_manuscrit') border-red-400 bg-red-50 @enderror"
            placeholder="Ex : Les Voix du Sahel" required>
        @error('titre_manuscrit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="bi bi-bookmarks mr-1 text-indigo-500"></i> Genre littéraire <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-4">
            @foreach(['Roman', 'Nouvelle'] as $genre)
            <label class="cursor-pointer">
                <input type="radio" name="genre_litteraire" value="{{ $genre }}"
                    {{ old('genre_litteraire', $data['genre_litteraire'] ?? '') === $genre ? 'checked' : '' }}
                    class="sr-only peer">
                <div class="w-full py-4 px-5 rounded-xl border-2 border-gray-200 text-center transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:border-indigo-300">
                    <i class="bi bi-{{ $genre == 'Roman' ? 'book-half' : 'journal-text' }} text-2xl block mb-1"></i>
                    <span class="font-semibold text-sm">{{ $genre }}</span>
                </div>
            </label>
            @endforeach
        </div>
        @error('genre_litteraire')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="resume" class="block text-sm font-semibold text-gray-700 mb-1.5">
            <i class="bi bi-card-text mr-1 text-indigo-500"></i> Résumé de l'œuvre <span class="text-red-500">*</span>
            <span class="ml-2 text-xs font-normal text-gray-400">(entre 150 et 300 mots)</span>
        </label>
        <textarea id="resume" name="resume" rows="8"
            class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-indigo-400 resize-none @error('resume') border-red-400 bg-red-50 @enderror"
            placeholder="Rédigez un résumé captivant de votre œuvre...">{{ old('resume', $data['resume'] ?? '') }}</textarea>
        <div class="flex justify-between mt-1">
            @error('resume')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @else
                <p class="text-xs text-gray-400">Rédigez entre 150 et 300 mots pour présenter votre œuvre.</p>
            @enderror
            <span id="word-count" class="text-xs text-gray-400 font-mono">0 mots</span>
        </div>
    </div>

    <div>
        <label for="nombre_mots" class="block text-sm font-semibold text-gray-700 mb-1.5">
            <i class="bi bi-123 mr-1 text-indigo-500"></i> Nombre de mots du manuscrit complet <span class="text-red-500">*</span>
        </label>
        <input type="number" id="nombre_mots" name="nombre_mots"
            value="{{ old('nombre_mots', $data['nombre_mots'] ?? '') }}"
            class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-indigo-400 @error('nombre_mots') border-red-400 bg-red-50 @enderror"
            min="1" placeholder="Ex : 80000">
        @error('nombre_mots')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="bi bi-journal-check mr-1 text-indigo-500"></i> Cette œuvre a-t-elle déjà été publiée ? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
                @foreach([1 => 'Oui', 0 => 'Non'] as $val => $label)
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="deja_publie" value="{{ $val }}"
                        {{ old('deja_publie', $data['deja_publie'] ?? '') == $val ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 text-center text-sm font-medium peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all hover:border-indigo-300">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
            @error('deja_publie')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="bi bi-send mr-1 text-indigo-500"></i> Est-elle soumise ailleurs ? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
                @foreach([1 => 'Oui', 0 => 'Non'] as $val => $label)
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="soumis_ailleurs" value="{{ $val }}"
                        {{ old('soumis_ailleurs', $data['soumis_ailleurs'] ?? '') == $val ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 text-center text-sm font-medium peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all hover:border-indigo-300">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
            @error('soumis_ailleurs')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex justify-between pt-4 border-t border-gray-100">
        <a href="{{ route('candidature.step1') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold hover:border-gray-300 hover:bg-gray-50 transition-all">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button type="submit"
            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-0.5">
            Suivant <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const resumeTextarea = document.getElementById('resume');
    const wordCountEl = document.getElementById('word-count');

    function countWords(text) {
        return text.trim() === '' ? 0 : text.trim().split(/\s+/u).length;
    }

    function updateWordCount() {
        const count = countWords(resumeTextarea.value);
        wordCountEl.textContent = `${count} mot${count > 1 ? 's' : ''}`;
        if (count < 150 || count > 300) {
            wordCountEl.classList.add('text-red-500');
            wordCountEl.classList.remove('text-green-500', 'text-gray-400');
        } else {
            wordCountEl.classList.add('text-green-500');
            wordCountEl.classList.remove('text-red-500', 'text-gray-400');
        }
    }

    resumeTextarea.addEventListener('input', updateWordCount);
    updateWordCount(); // initial count
</script>
@endpush
