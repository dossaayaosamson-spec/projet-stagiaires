@extends('layouts.candidature')

@section('title', 'Candidature envoyée avec succès · ECLA')

@section('candidature_content')
<div class="text-center py-8">
    <div class="relative inline-flex items-center justify-center mb-8">
        <div class="absolute w-32 h-32 rounded-full bg-gradient-to-br from-emerald-200 to-teal-300 animate-ping opacity-20"></div>
        <div class="relative w-28 h-28 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-2xl shadow-emerald-200">
            <i class="bi bi-check-lg text-white text-5xl"></i>
        </div>
    </div>

    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 font-montserrat mb-4"> 
        <i class="bi bi-check-lg text-emerald-600"></i> Candidature reçue !
    </h2>
    <p class="text-lg text-gray-600 max-w-xl mx-auto font-lora mb-3">
        Votre candidature au concours <span class="font-bold text-emerald-700">eCLA 2026</span> a bien été enregistrée.
        Un email de confirmation vous a été envoyé.
    </p>
    <p class="text-gray-500 text-sm max-w-lg mx-auto">
        Notre équipe éditoriale examinera votre manuscrit avec soin. Vous serez contacté(e) par e-mail pour la suite du processus.
    </p>

    <div class="mt-10 max-w-md mx-auto text-left">
        <h3 class="text-lg font-bold text-gray-800 mb-5 font-montserrat flex items-center gap-2">
            <i class="bi bi-calendar-check text-emerald-600"></i> Prochaines étapes
        </h3>

        @php
            $steps = [
                ['icon' => 'bi-envelope-check', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100', 'title' => 'Email de confirmation', 'desc' => 'Vous venez de recevoir un e-mail récapitulatif de votre candidature.'],
                ['icon' => 'bi-search', 'color' => 'text-violet-600', 'bg' => 'bg-violet-100', 'title' => 'Examen du manuscrit', 'desc' => 'Notre jury littéraire lira et évaluera votre œuvre dans les semaines à venir.'],
                ['icon' => 'bi-trophy', 'color' => 'text-amber-600', 'bg' => 'bg-amber-100', 'title' => 'Annonce des résultats', 'desc' => 'Les auteur(e)s sélectionné(e)s seront contacté(e)s par e-mail et annoncé(e)s sur nos réseaux.'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach($steps as $step)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/70 border border-gray-100 shadow-sm">
                <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $step['bg'] }} flex items-center justify-center">
                    <i class="bi {{ $step['icon'] }} {{ $step['color'] }} text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $step['title'] }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-10 pt-8 border-t border-gray-100">
        <p class="text-sm text-gray-500 mb-5">Partagez la nouvelle sur vos réseaux !</p>
        <div class="flex justify-center gap-3 flex-wrap">
            <a href="https://wa.me/?text={{ urlencode('Je viens de soumettre ma candidature au concours eCLA 2026 organisé par AGAU ÉDITIONS ! ') }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition-all shadow-md shadow-green-200">
                <i class="bi bi-whatsapp text-base"></i> WhatsApp
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/')) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all shadow-md shadow-blue-200">
                <i class="bi bi-facebook text-base"></i> Facebook
            </a>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition-all">
                <i class="bi bi-house text-base"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
