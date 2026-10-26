@extends('layouts.candidature')

@section('title', 'Étape 3 — Manuscrit · ECLA')

@section('candidature_content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-violet-100 text-violet-700 font-bold text-lg">3</span>
        <h2 class="text-2xl font-extrabold text-gray-900 font-montserrat">Téléversement du manuscrit</h2>
    </div>
    <p class="text-gray-500 font-lora">Déposez votre manuscrit au format DOCX. Taille maximale : 10 Mo.</p>
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

<form action="{{ route('candidature.step3') }}" method="POST" enctype="multipart/form-data" id="step3-form" class="space-y-6" novalidate>
    @csrf

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="bi bi-file-earmark-word mr-1 text-violet-500"></i> Fichier manuscrit (.docx) <span class="text-red-500">*</span>
        </label>

        <div id="drop-zone"
            class="relative flex flex-col items-center justify-center w-full min-h-56 rounded-2xl border-2 border-dashed border-violet-300 bg-violet-50/50 cursor-pointer transition-all duration-300 hover:border-violet-500 hover:bg-violet-50 group @error('fichier_manuscrit') border-red-400 bg-red-50 @enderror">
            <input type="file" id="fichier_manuscrit" name="fichier_manuscrit" accept=".docx"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

            <div id="upload-placeholder" class="text-center px-8 py-8 pointer-events-none">
                <div class="mb-4">
                    <i class="bi bi-cloud-upload text-6xl text-violet-300 group-hover:text-violet-500 transition-colors duration-300"></i>
                </div>
                <p class="text-base font-semibold text-gray-600 mb-1">Glissez votre fichier ici</p>
                <p class="text-sm text-gray-400">ou cliquez pour parcourir vos fichiers</p>
                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full border border-violet-200 text-xs text-violet-600 font-medium">
                    <i class="bi bi-shield-check"></i> Format .DOCX uniquement · Max 10 Mo
                </div>
            </div>

            <div id="file-preview" class="hidden text-center px-8 py-8 pointer-events-none">
                <div class="mb-3">
                    <i class="bi bi-file-earmark-word-fill text-6xl text-blue-500"></i>
                </div>
                <p id="file-name" class="text-base font-bold text-gray-800 mb-1 truncate max-w-xs mx-auto"></p>
                <p id="file-size" class="text-sm text-gray-500"></p>
                <p class="mt-3 text-xs text-violet-600 font-medium"><i class="bi bi-arrow-repeat mr-1"></i>Cliquez pour changer de fichier</p>
            </div>
        </div>

        @error('fichier_manuscrit')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="bi bi-x-circle"></i> {{ $message }}</p>@enderror

        @if(!empty($data['original_filename']))
        <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2 text-sm text-green-700">
            <i class="bi bi-check-circle-fill text-green-500"></i>
            Fichier déjà uploadé : <span class="font-semibold">{{ $data['original_filename'] }}</span>
            <span class="ml-auto text-xs text-green-500">Vous pouvez en choisir un autre si nécessaire.</span>
        </div>
        @endif
    </div>

    <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl">
        <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
            <i class="bi bi-info-circle-fill text-amber-500"></i> Consignes de soumission
        </h4>
        <ul class="space-y-2 text-sm text-amber-700">
            <li class="flex items-start gap-2"><i class="bi bi-check2 mt-0.5 flex-shrink-0"></i> Le fichier doit être au format <strong>Microsoft Word (.docx)</strong></li>
            <li class="flex items-start gap-2"><i class="bi bi-check2 mt-0.5 flex-shrink-0"></i> Taille maximale de <strong>10 Mo</strong></li>
            <li class="flex items-start gap-2"><i class="bi bi-check2 mt-0.5 flex-shrink-0"></i> Votre nom ne doit <strong>pas apparaître</strong> dans le manuscrit (anonymat)</li>
            <li class="flex items-start gap-2"><i class="bi bi-check2 mt-0.5 flex-shrink-0"></i> Utilisez une police standard : <strong>Times New Roman 12pt ou Arial 12pt</strong></li>
            <li class="flex items-start gap-2"><i class="bi bi-check2 mt-0.5 flex-shrink-0"></i> Interligne : <strong>1.5</strong></li>
        </ul>
    </div>

    <div class="flex justify-between pt-4 border-t border-gray-100">
        <a href="{{ route('candidature.step2') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold hover:border-gray-300 hover:bg-gray-50 transition-all">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button type="submit"
            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 text-white font-bold shadow-lg shadow-violet-200 hover:shadow-violet-300 hover:from-violet-700 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-0.5">
            Suivant <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('fichier_manuscrit');
    const placeholder = document.getElementById('upload-placeholder');
    const preview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const sizeMb = (file.size / 1024 / 1024).toFixed(2);
            fileName.textContent = file.name;
            fileSize.textContent = `${sizeMb} Mo`;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
        }
    });
</script>
@endpush
