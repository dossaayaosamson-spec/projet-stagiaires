@extends('layouts.auth')

@section('title', 'Réinitialisation du mot de passe — AGAU Éditions')

@section('left-panel')
<div class="relative z-[2]">
    <h1 class="font-serif text-[28px] min-[900px]:text-[38px] text-white font-semibold leading-[1.2] mb-[20px] max-w-[450px]">
        Créez un nouveau <br>
        <strong class="text-[#5BC4F5] italic font-semibold">mot de passe sécurisé.</strong>
    </h1>
    <p class="text-[14px] min-[900px]:text-[15px] text-white/55 leading-[1.8] max-w-[380px] mb-[40px] font-sans font-normal">
        Choisissez un mot de passe fort d'au moins 8 caractères pour protéger votre compte AGAU Éditions.
    </p>
</div>
@endsection

@section('right-panel')
<div class="max-w-[420px] w-full mx-auto relative z-[2]">
    <div class="mb-[32px]">
        <h2 class="font-serif text-[26px] text-agau-text font-semibold m-0 mb-[8px]">Nouveau mot de passe</h2>
        <p class="text-[0.85rem] text-agau-muted m-0">Renseignez votre nouveau mot de passe ci-dessous.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-[20px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="email">Adresse e-mail</label>
            <div class="relative">
                <input type="email" id="email" name="email" required
                    class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[14px] pl-[44px] pr-[16px] text-[14.5px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10"
                    placeholder="exemple@email.com"
                    value="{{ old('email', $email) }}">
                <span class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[16px] text-agau-muted select-none pointer-events-none">✉</span>
            </div>
            @error('email')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-[20px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="password">Nouveau mot de passe</label>
            <div class="relative">
                <input type="password" id="password" name="password" required
                    class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[14px] pl-[44px] pr-[48px] text-[14.5px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10"
                    placeholder="Minimum 8 caractères">
                <span class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[16px] text-agau-muted select-none pointer-events-none"><i class="bi bi-lock"></i></span>
                <button type="button" class="toggle-password absolute right-[16px] top-1/2 -translate-y-1/2 bg-transparent border-none text-[16px] text-agau-muted cursor-pointer hover:text-agau-blue focus:outline-none">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-[28px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="password_confirmation">Confirmer le mot de passe</label>
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[14px] pl-[44px] pr-[48px] text-[14.5px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10"
                    placeholder="Répétez votre mot de passe">
                <span class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[16px] text-agau-muted select-none pointer-events-none"><i class="bi bi-shield-lock"></i></span>
                <button type="button" class="toggle-password absolute right-[16px] top-1/2 -translate-y-1/2 bg-transparent border-none text-[16px] text-agau-muted cursor-pointer hover:text-agau-blue focus:outline-none">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="w-full bg-agau-blue text-white border-none py-[16px] text-[12.5px] font-bold tracking-[1.5px] uppercase rounded-[6px] cursor-pointer transition-all duration-200 shadow-[0_4px_14px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:shadow-[0_6px_20px_rgba(27,108,168,0.35)] hover:-translate-y-[2px] focus:outline-none focus:ring-4 focus:ring-agau-blue/30">
            Réinitialiser mon mot de passe
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
});
</script>
@endsection
