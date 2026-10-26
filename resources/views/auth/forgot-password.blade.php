@extends('layouts.auth')

@section('title', 'Mot de passe oublié — AGAU Éditions')

@section('left-panel')
<div class="relative z-[2]">
    <h1 class="font-serif text-[28px] min-[900px]:text-[38px] text-white font-semibold leading-[1.2] mb-[20px] max-w-[450px]">
        On a tous oublié <br>
        <strong class="text-[#5BC4F5] italic font-semibold">un mot de passe.</strong>
    </h1>
    <p class="text-[14px] min-[900px]:text-[15px] text-white/55 leading-[1.8] max-w-[380px] mb-[40px] font-sans font-normal">
        Pas de panique ! Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre accès.
    </p>
</div>
@endsection

@section('right-panel')
<div class="max-w-[420px] w-full mx-auto relative z-[2]">
    <div class="mb-[32px]">
        <h2 class="font-serif text-[26px] text-agau-text font-semibold m-0 mb-[8px]">Mot de passe oublié ?</h2>
        <p class="text-[0.85rem] text-agau-muted m-0">Renseignez votre e-mail pour recevoir un lien de réinitialisation.</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-4 rounded-lg mb-6 font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-[24px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="email">Adresse e-mail</label>
            <div class="relative">
                <input type="email" id="email" name="email" autofocus required
                    class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[14px] pl-[44px] pr-[16px] text-[14.5px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10"
                    placeholder="exemple@email.com"
                    value="{{ old('email') }}">
                <span class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[16px] text-agau-muted select-none pointer-events-none">✉</span>
            </div>
            @error('email')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-agau-blue text-white border-none py-[16px] text-[12.5px] font-bold tracking-[1.5px] uppercase rounded-[6px] cursor-pointer transition-all duration-200 shadow-[0_4px_14px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:shadow-[0_6px_20px_rgba(27,108,168,0.35)] hover:-translate-y-[2px] focus:outline-none focus:ring-4 focus:ring-agau-blue/30">
            Envoyer le lien de réinitialisation
        </button>
    </form>

    <p class="text-center text-[14px] text-agau-text mt-[32px] font-medium">
        <a href="{{ route('login') }}" class="text-agau-blue font-bold no-underline">← Retour à la connexion</a>
    </p>
</div>
@endsection
