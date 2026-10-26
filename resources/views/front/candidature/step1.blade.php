@extends('layouts.candidature')

@section('title', 'Étape 1 — Informations personnelles · ECLA')

@section('candidature_content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold text-lg">1</span>
        <h2 class="text-2xl font-extrabold text-gray-900 font-montserrat">Vos informations personnelles</h2>
    </div>
    <p class="text-gray-500 ml-13 font-lora">Ces informations permettent de vous identifier en tant que candidat(e) au concours ECLA.</p>
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

<form action="{{ route('candidature.step1') }}" method="POST" class="space-y-6" id="step1-form" novalidate>
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="nom" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-person-fill mr-1 text-blue-500"></i> Nom de famille <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $data['nom'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('nom') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Dupont" required>
            @error('nom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="prenoms" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-person mr-1 text-blue-500"></i> Prénom(s) <span class="text-red-500">*</span>
            </label>
            <input type="text" id="prenoms" name="prenoms" value="{{ old('prenoms', $data['prenoms'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('prenoms') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Marie" required>
            @error('prenoms')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-gender-ambiguous mr-1 text-blue-500"></i> Genre <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4 mt-1">
                @foreach(['Masculin', 'Féminin'] as $g)
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="genre" value="{{ $g }}" {{ old('genre', $data['genre'] ?? '') === $g ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 text-center text-sm font-medium peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all hover:border-blue-300">
                        <i class="bi bi-{{ $g == 'Masculin' ? 'gender-male' : 'gender-female' }} mr-1"></i>{{ $g }}
                    </div>
                </label>
                @endforeach
            </div>
            @error('genre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="age" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-calendar3 mr-1 text-blue-500"></i> Âge <span class="text-red-500">*</span>
            </label>
            <input type="number" id="age" name="age" value="{{ old('age', $data['age'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('age') border-red-400 bg-red-50 @enderror"
                min="1" max="120" placeholder="Ex : 28">
            @error('age')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="nationalite" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-flag mr-1 text-blue-500"></i> Nationalité <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nationalite" name="nationalite" value="{{ old('nationalite', $data['nationalite'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('nationalite') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Togolaise">
            @error('nationalite')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="pays_residence" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-globe-americas mr-1 text-blue-500"></i> Pays de résidence <span class="text-red-500">*</span>
            </label>
            <input type="text" id="pays_residence" name="pays_residence" value="{{ old('pays_residence', $data['pays_residence'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('pays_residence') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Togo">
            @error('pays_residence')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="ville_residence" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-building mr-1 text-blue-500"></i> Ville de résidence <span class="text-red-500">*</span>
            </label>
            <input type="text" id="ville_residence" name="ville_residence" value="{{ old('ville_residence', $data['ville_residence'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('ville_residence') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Lomé">
            @error('ville_residence')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="quartier" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-geo-alt mr-1 text-blue-500"></i> Quartier <span class="text-red-500">*</span>
            </label>
            <input type="text" id="quartier" name="quartier" value="{{ old('quartier', $data['quartier'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('quartier') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Adidogomé">
            @error('quartier')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="whatsapp" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-whatsapp mr-1 text-green-500"></i> Numéro WhatsApp <span class="text-red-500">*</span>
            </label>
            <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $data['whatsapp'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('whatsapp') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : +228 90 00 00 00">
            @error('whatsapp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-envelope mr-1 text-blue-500"></i> Adresse e-mail <span class="text-red-500">*</span>
            </label>
            <input type="email" id="email" name="email" value="{{ old('email', $data['email'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('email') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : marie@example.com">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label for="profession" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <i class="bi bi-briefcase mr-1 text-blue-500"></i> Profession <span class="text-red-500">*</span>
            </label>
            <input type="text" id="profession" name="profession" value="{{ old('profession', $data['profession'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-blue-400 @error('profession') border-red-400 bg-red-50 @enderror"
                placeholder="Ex : Enseignant(e), Étudiant(e), Journaliste...">
            @error('profession')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex justify-end pt-4 border-t border-gray-100">
        <button type="submit"
            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold shadow-lg shadow-blue-200 hover:shadow-blue-300 hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5">
            Suivant <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
