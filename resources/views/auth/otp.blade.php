@extends('layouts.auth')

@section('title', 'Vérification 2FA — AGAU Éditions')

@section('left-panel')
<div class="relative z-[2]">
    <h1 class="font-serif text-[28px] min-[900px]:text-[38px] text-white font-semibold leading-[1.2] mb-[20px] max-w-[450px]">
        Sécurité renforcée <br>
        <strong class="text-[#5BC4F5] italic font-semibold">de votre compte.</strong>
    </h1>
    <p class="text-[14px] min-[900px]:text-[15px] text-white/55 leading-[1.8] max-w-[380px] mb-[40px] font-sans font-normal">
        Un code de sécurité temporaire à 6 chiffres vous a été envoyé par e-mail afin de valider votre accès.
    </p>

    <div class="flex gap-[30px] border-t border-white/10 pt-[24px]">
        <div>
            <div class="text-[20px] min-[900px]:text-[24px] font-black text-white">6 Chiffres</div>
            <div class="text-[9px] min-[900px]:text-[10px] text-[#5BC4F5] tracking-[1.5px] uppercase mt-[3px]">Code Unique</div>
        </div>
        <div>
            <div class="text-[20px] min-[900px]:text-[24px] font-black text-white">10 Min</div>
            <div class="text-[9px] min-[900px]:text-[10px] text-[#5BC4F5] tracking-[1.5px] uppercase mt-[3px]">Validité</div>
        </div>
    </div>
</div>
@endsection

@section('right-panel')
<div class="max-w-[420px] w-full mx-auto relative z-[2]">
    <div class="text-center mb-[32px]">
        <div class="w-16 h-16 bg-agau-blue/10 text-agau-blue rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h2 class="font-serif text-[24px] min-[900px]:text-[28px] text-agau-text font-semibold m-0">Vérification de sécurité</h2>
        <p class="text-[13px] text-agau-muted mt-[8px]">
            Saisissez le code à 6 chiffres envoyé à <br>
            <strong class="text-agau-text">{{ $maskedEmail ?? 'votre adresse e-mail' }}</strong>
        </p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs p-3 rounded-lg mb-6 font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs p-3 rounded-lg mb-6 font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- OTP Form -->
    <form method="POST" action="{{ route('login.otp') }}">
        @csrf
        
        <div class="mb-[24px]">
            <label class="block text-center text-[12px] font-bold uppercase tracking-wider text-agau-muted mb-[12px]" for="otp_code">Code de vérification (6 chiffres)</label>
            <div class="relative">
                <input type="text" id="otp_code" name="otp_code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required autofocus
                    class="w-full bg-[#fcfcfc] border border-agau-border rounded-[8px] py-[16px] text-center text-[28px] font-mono font-bold tracking-[10px] text-agau-blue transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 placeholder:tracking-normal placeholder:font-sans placeholder:text-sm placeholder:text-agau-muted/40"
                    placeholder="000000">
            </div>
            @error('otp_code')
                <p class="text-[#E24B4A] text-[12px] text-center mt-[8px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-agau-blue text-white border-none py-[16px] text-[12.5px] font-bold tracking-[1.5px] uppercase rounded-[6px] cursor-pointer transition-all duration-200 shadow-[0_4px_14px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:shadow-[0_6px_20px_rgba(27,108,168,0.35)] hover:-translate-y-[2px] focus:outline-none focus:ring-4 focus:ring-agau-blue/30">
            Valider la connexion
        </button>
    </form>

    <div class="mt-[24px] pt-[20px] border-t border-agau-border/40 text-center space-y-3">
        <form method="POST" action="{{ route('login.otp.resend') }}" class="m-0">
            @csrf
            <button type="submit" class="text-xs text-agau-blue font-bold bg-transparent border-none cursor-pointer hover:underline">
                <i class="bi bi-arrow-clockwise"></i> Renvoyer un nouveau code par e-mail
            </button>
        </form>

        <div>
            <a href="{{ route('login') }}" class="text-xs text-agau-muted hover:text-agau-text font-medium no-underline">
                ← Retour à la page de connexion
            </a>
        </div>
    </div>
</div>
@endsection
