@extends('layouts.auth')

@section('title', 'Vérification — AGAU Éditions')

@section('left-panel')
<div class="relative z-[2]">
    <h1 class="font-serif text-[28px] min-[900px]:text-[38px] text-white font-semibold leading-[1.2] mb-[20px] max-w-[450px]">
        Une dernière étape pour <br>
        <strong class="text-[#5BC4F5] italic font-semibold">confirmer votre passion.</strong>
    </h1>
    <p class="text-[14px] min-[900px]:text-[15px] text-white/55 leading-[1.8] max-w-[380px] mb-[40px] font-sans font-normal">
        Nous venons de vous envoyer un code de confirmation à 6 chiffres par email. Veuillez le saisir ci-contre pour activer votre compte.
    </p>
</div>
@endsection

@section('right-panel')
<div class="max-w-[420px] w-full mx-auto relative z-[2]">
    <div class="text-center mb-[32px]">
        <div class="w-[64px] h-[64px] bg-agau-blue/10 rounded-full flex items-center justify-center mx-auto mb-[24px]">
            <span class="text-[32px]"><i class="fas fa-envelope"></i></span>
        </div>
        <h2 class="font-serif text-[26px] min-[900px]:text-[32px] text-agau-text font-semibold m-0">Consultez vos emails</h2>
        <p class="text-[14px] text-agau-muted mt-[12px] leading-[1.6]">
            Saisissez le code de vérification envoyé à l'adresse <br><strong class="text-agau-text">{{ auth()->user()->email }}</strong>
        </p>
    </div>

    @if (session('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-[16px] py-[12px] rounded-[6px] text-[13px] font-medium mb-[24px] flex items-center gap-[10px]">
            <span class="text-[16px]"><i class="fas fa-check"></i></span>
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->has('verification_code'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-[16px] py-[12px] rounded-[6px] text-[13px] font-medium mb-[24px] flex items-center gap-[10px]">
            <span class="text-[16px]"><i class="fas fa-exclamation-triangle"></i></span>
            {{ $errors->first('verification_code') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}" id="otp-form">
        @csrf
        <div class="flex justify-center gap-2 md:gap-3 mb-8" id="otp-inputs">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" name="code[]" maxlength="1" 
                    class="w-11 h-14 md:w-14 md:h-16 text-center text-2xl font-bold bg-white border-2 border-agau-border rounded-xl focus:outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 transition-all text-agau-text shadow-sm" 
                    data-index="{{ $i }}" autocomplete="off" inputmode="numeric" {{ $i === 0 ? 'autofocus' : '' }}>
            @endfor
        </div>

        <button type="submit" class="w-full bg-agau-blue text-white border-none py-[16px] text-[12.5px] font-bold tracking-[1.5px] uppercase rounded-[6px] cursor-pointer transition-all duration-200 shadow-[0_4px_14px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:shadow-[0_6px_20px_rgba(27,108,168,0.35)] hover:-translate-y-[2px] focus:outline-none focus:ring-4 focus:ring-agau-blue/30 mt-[8px]">
            Vérifier mon compte
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-[13px] text-agau-muted">Vous n'avez pas reçu le code ?</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="bg-transparent border-none text-[13px] text-agau-blue font-bold cursor-pointer hover:underline mt-1 p-0">
                Renvoyer un nouveau code
            </button>
        </form>
    </div>

    <div class="mt-[32px] pt-[24px] border-t border-agau-border flex justify-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-transparent border-none text-[13px] text-agau-muted font-bold cursor-pointer hover:text-agau-blue transition-colors">
                Se déconnecter
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('#otp-inputs input');
    
    inputs.forEach((input, index) => {
        // Gérer la saisie
        input.addEventListener('input', (e) => {
            if (e.inputType === "deleteContentBackward") return;
            
            const val = input.value;
            if (val.length > 0) {
                input.value = val.charAt(val.length - 1); // Garder seulement le dernier caractère
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });

        // Gérer le retour arrière et les flèches
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '' && index > 0) {
                inputs[index - 1].focus();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                inputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // Gérer le collage (Paste)
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const data = e.clipboardData.getData('text').trim();
            if (!/^\d+$/.test(data)) return; // Seulement des chiffres

            const digits = data.split('').slice(0, 6);
            digits.forEach((digit, i) => {
                if (inputs[i]) {
                    inputs[i].value = digit;
                }
            });
            
            // Focus sur le dernier input rempli ou le bouton
            const nextFocus = digits.length < 6 ? inputs[digits.length] : inputs[5];
            nextFocus.focus();
        });
    });
});
</script>
@endsection
