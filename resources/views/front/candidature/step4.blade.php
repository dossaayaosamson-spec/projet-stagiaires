@extends('layouts.candidature')

@section('title', 'Étape 4 — Engagement · ECLA')

@section('candidature_content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold text-lg">4</span>
        <h2 class="text-2xl font-extrabold text-gray-900 font-montserrat">Engagement du candidat</h2>
    </div>
    <p class="text-gray-500 font-lora">Avant de finaliser votre candidature, veuillez lire et valider les engagements ci-dessous.</p>
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

<form action="{{ route('candidature.step4') }}" method="POST" id="step4-form" class="space-y-6" novalidate>
    @csrf

    <div class="p-6 bg-white/80 rounded-2xl border border-gray-200 space-y-4">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4">
            <i class="bi bi-pencil-square text-emerald-600 text-lg"></i>
            Déclaration sur l'honneur
        </h3>

        @php
            $declarations = [
                'originalite_1' => "Je certifie que l'œuvre soumise est entièrement originale et m'appartient.",
                'originalite_2' => "Je suis bien l'auteur(e) unique de ce manuscrit et il n'a pas été rédigé par une intelligence artificielle.",
                'originalite_3' => "Cette œuvre ne viole aucun droit d'auteur et ne contient aucun contenu plagié.",
                'originalite_4' => "Toutes les informations fournies dans ce formulaire sont exactes et véridiques.",
                'originalite_5' => "J'accepte les conditions générales du concours eCLA 2026 organisé par AGAU ÉDITIONS.",
            ];
        @endphp

        @foreach($declarations as $name => $label)
        <label class="flex items-start gap-3 cursor-pointer group" id="{{ $name }}-label">
            <div class="relative mt-0.5">
                <input type="checkbox" name="{{ $name }}" value="1"
                    {{ old($name, $data[$name] ?? '') ? 'checked' : '' }}
                    class="sr-only peer" id="{{ $name }}">
                <div class="w-5 h-5 rounded border-2 border-gray-300 bg-white peer-checked:bg-emerald-500 peer-checked:border-emerald-500 transition-all flex items-center justify-center">
                    <i class="bi bi-check-lg text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </div>
            </div>
            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors leading-relaxed">{{ $label }}</span>
        </label>
        @endforeach
    </div>

    <div class="p-5 bg-emerald-50/70 border border-emerald-200 rounded-2xl">
        <label class="flex items-start gap-3 cursor-pointer group" id="acceptation-label">
            <div class="relative mt-0.5">
                <input type="checkbox" name="acceptation_reglement" value="1"
                    {{ old('acceptation_reglement', $data['acceptation_reglement'] ?? '') ? 'checked' : '' }}
                    class="sr-only peer" id="acceptation_reglement">
                <div class="w-5 h-5 rounded border-2 border-emerald-400 bg-white peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all">
                    <i class="bi bi-check-lg text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center h-full"></i>
                </div>
            </div>
            <span class="text-sm font-medium text-emerald-800 leading-relaxed">
                <i class="bi bi-file-earmark-text mr-1"></i>
                J'ai lu et j'accepte intégralement le <a href="#" class="underline font-bold hover:text-emerald-600">règlement officiel du concours eCLA 2026</a>.
            </span>
        </label>
    </div>

    <div class="p-5 bg-white/80 rounded-2xl border border-gray-200">
        <label class="block text-sm font-semibold text-gray-700 mb-3">
            <i class="bi bi-person-circle mr-1 text-blue-500"></i>
            Avez-vous un compte sur le site d'AGAU ÉDITIONS ? <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-4">
            @foreach([1 => 'Oui, j\'ai un compte', 0 => 'Non, pas encore'] as $val => $label)
            <label class="flex-1 cursor-pointer">
                <input type="radio" name="compte_agau" value="{{ $val }}"
                    {{ old('compte_agau', $data['compte_agau'] ?? '') == $val ? 'checked' : '' }}
                    class="sr-only peer">
                <div class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 text-center text-sm font-medium peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all hover:border-blue-300">
                    <i class="bi bi-{{ $val ? 'check-circle' : 'x-circle' }} mr-1"></i>{{ $label }}
                </div>
            </label>
            @endforeach
        </div>
        @error('compte_agau')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="p-5 bg-white/80 rounded-2xl border border-gray-200">
        <label for="source_decouverte" class="block text-sm font-semibold text-gray-700 mb-3">
            <i class="bi bi-megaphone mr-1 text-orange-500"></i>
            Comment avez-vous entendu parler du concours ? <span class="text-red-500">*</span>
        </label>

        @php
            $sources = ['WhatsApp', 'Facebook', 'Instagram', 'TikTok', 'Université', 'Média/Presse', 'Site web de AGAU ÉDITIONS', 'Autre'];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="source-grid">
            @foreach($sources as $src)
            <label class="cursor-pointer">
                <input type="radio" name="source_decouverte" value="{{ $src }}"
                    {{ old('source_decouverte', $data['source_decouverte'] ?? '') === $src ? 'checked' : '' }}
                    class="sr-only peer source-radio">
                <div class="w-full py-2.5 px-3 rounded-xl border-2 border-gray-200 text-center text-xs font-semibold peer-checked:border-orange-400 peer-checked:bg-orange-50 peer-checked:text-orange-700 transition-all hover:border-orange-300">
                    {{ $src }}
                </div>
            </label>
            @endforeach
        </div>

        <div id="autre-field" class="{{ old('source_decouverte', $data['source_decouverte'] ?? '') === 'Autre' ? '' : 'hidden' }} mt-4">
            <input type="text" name="source_decouverte_autre"
                value="{{ old('source_decouverte_autre', $data['source_decouverte_autre'] ?? '') }}"
                class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:border-orange-400"
                placeholder="Précisez la source...">
        </div>

        @error('source_decouverte')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        @error('source_decouverte_autre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-between pt-4 border-t border-gray-100">
        <a href="{{ route('candidature.step3') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold hover:border-gray-300 hover:bg-gray-50 transition-all">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button type="submit" id="submit-btn"
            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg shadow-emerald-200 hover:shadow-emerald-300 hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
            <i class="bi bi-send-fill"></i> Soumettre ma candidature
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.source-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const autreField = document.getElementById('autre-field');
            autreField.classList.toggle('hidden', this.value !== 'Autre');
        });
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function () {
            const dot = this.nextElementSibling?.querySelector('i');
            if (dot) dot.classList.toggle('opacity-100', this.checked);
        });
    });
</script>
@endpush
