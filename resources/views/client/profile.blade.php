@extends('layouts.dashboard')

@section('title', 'Mon Profil — AGAU Éditions')

@section('content')
<div class="max-w-[800px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Informations <span class="text-agau-blue">Personnelles</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Gérez votre compte et vos préférences.</p>
    </div>

    <form method="POST" action="{{ route('client.profile.update') }}" class="bg-white border border-agau-border rounded-[10px] p-[32px] shadow-[0_4px_20px_rgba(15,74,122,0.03)] flex flex-col gap-[24px]">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px]">
            <div>
                <label class="block text-[0.75rem] font-bold text-agau-text mb-[8px]" for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="{{ $user->prenom }}" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] px-[16px] text-[0.9rem] text-agau-text outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 transition-all duration-200" required>
            </div>
            <div>
                <label class="block text-[0.75rem] font-bold text-agau-text mb-[8px]" for="nom_famille">Nom</label>
                <input type="text" id="nom_famille" name="nom_famille" value="{{ $user->nom_famille }}" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] px-[16px] text-[0.9rem] text-agau-text outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 transition-all duration-200" required>
            </div>
        </div>

        <div>
            <label class="block text-[0.75rem] font-bold text-agau-text mb-[8px]" for="email">Adresse email</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] px-[16px] text-[0.9rem] text-agau-text outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 transition-all duration-200" required>
        </div>

        <div>
            <label class="block text-[0.75rem] font-bold text-agau-text mb-[8px]" for="telephone">Numéro de téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="{{ $user->telephone }}" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] px-[16px] text-[0.9rem] text-agau-text outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 transition-all duration-200" required>
        </div>

        <div class="pt-[16px] flex justify-end">
            <button type="submit" class="bg-agau-blue text-white border-none py-[14px] px-[40px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 shadow-[0_4px_12px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:-translate-y-[2px]">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
