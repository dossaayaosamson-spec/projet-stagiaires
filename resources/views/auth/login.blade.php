@extends('layouts.auth')

@section('title', 'Connexion — AGAU Éditions')

@section('left-panel')
<div class="relative z-[2]">
    <h1 class="font-serif text-[28px] min-[900px]:text-[38px] text-white font-semibold leading-[1.2] mb-[20px] max-w-[450px]">
        Heureux de vous revoir parmi <br>
        <strong class="text-[#5BC4F5] italic font-semibold">les grands esprits.</strong>
    </h1>
    <p class="text-[14px] min-[900px]:text-[15px] text-white/55 leading-[1.8] max-w-[380px] mb-[40px] font-sans font-normal">
        Connectez-vous pour retrouver vos lectures en cours, vos favoris et accéder à votre bibliothèque personnelle.
    </p>

    <!-- Stats Mini -->
    <div class="flex gap-[30px] border-t border-white/10 pt-[24px]">
        <div>
            <div class="text-[20px] min-[900px]:text-[24px] font-black text-white">+{{ $booksCount ?? \Illuminate\Support\Facades\Cache::remember('welcome_books_count', 1440, fn () => \App\Models\Book::count()) }}</div>
            <div class="text-[9px] min-[900px]:text-[10px] text-[#5BC4F5] tracking-[1.5px] uppercase mt-[3px]">Ouvrages</div>
        </div>
        <div>
            <div class="text-[20px] min-[900px]:text-[24px] font-black text-white">+{{ number_format($readersCount ?? \Illuminate\Support\Facades\Cache::remember('welcome_readers_count', 1440, fn () => \App\Models\User::count()), 0, ',', ' ') }}</div>
            <div class="text-[9px] min-[900px]:text-[10px] text-[#5BC4F5] tracking-[1.5px] uppercase mt-[3px]">Lecteurs actifs</div>
        </div>
    </div>
</div>
@endsection

@section('right-panel')
<div class="max-w-[420px] w-full mx-auto relative z-[2]">
    <div class="text-center mb-[40px]">
        <h2 class="font-serif text-[26px] min-[900px]:text-[32px] text-agau-text font-semibold m-0">Connexion</h2>
        <p class="text-[14px] text-agau-muted mt-[10px]">Accédez à votre compte lecteur</p>
    </div>

    {{-- 
    <div class="flex flex-col min-[480px]:flex-row gap-[16px] mb-[32px]">
        <button type="button" class="flex-1 flex items-center justify-center gap-[10px] bg-white border border-agau-border py-[12px] px-[20px] rounded-[50px] text-[13px] font-semibold text-agau-text cursor-pointer transition-all duration-200 hover:bg-agau-off-white hover:-translate-y-[2px] shadow-[0_4px_10px_rgba(0,0,0,0.03)] focus:outline-none focus:ring-2 focus:ring-agau-blue/50">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
        </button>
    </div>

    <div class="flex items-center gap-[16px] mb-[32px]">
        <div class="h-[1px] bg-agau-border flex-1"></div>
        <div class="text-[12px] text-agau-muted font-medium uppercase tracking-[1px]">Ou avec un email</div>
        <div class="h-[1px] bg-agau-border flex-1"></div>
    </div>
    --}}

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="mb-[20px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="email">Adresse email</label>
            <div class="relative">
                <input type="email" id="email" name="email" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[14px] pl-[44px] pr-[16px] text-[14.5px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10" placeholder="exemple@email.com" required value="{{ old('email') }}">
                <span class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[16px] text-agau-muted select-none pointer-events-none">✉</span>
            </div>
            @error('email')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-[24px]">
            <div class="flex justify-between items-center mb-[8px]">
                <label class="block text-[13px] font-bold text-agau-text m-0" for="password">Mot de passe</label>
                <a href="{{ route('password.request') }}" class="text-[12px] text-agau-blue font-semibold no-underline transition-colors duration-200 hover:text-agau-light">Mot de passe oublié ?</a>
            </div>
            <div class="relative">
                <input type="password" id="password" name="password" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[14px] pl-[44px] pr-[16px] text-[14.5px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10" placeholder="••••••••" required>
                <span class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[16px] text-agau-muted select-none pointer-events-none"><i class="bi bi-lock"></i></span>
                <!-- Password visibility toggle -->
                <button type="button" class="toggle-password absolute right-[16px] top-1/2 -translate-y-1/2 bg-transparent border-none text-[16px] text-agau-muted cursor-pointer transition-colors duration-200 hover:text-agau-blue focus:outline-none">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-agau-blue text-white border-none py-[16px] text-[12.5px] font-bold tracking-[1.5px] uppercase rounded-[6px] cursor-pointer transition-all duration-200 shadow-[0_4px_14px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:shadow-[0_6px_20px_rgba(27,108,168,0.35)] hover:-translate-y-[2px] focus:outline-none focus:ring-4 focus:ring-agau-blue/30 mt-[8px]">
            Se connecter
        </button>
    </form>

    <p class="text-center text-[14px] text-agau-text mt-[32px] font-medium">
        Vous n'avez pas de compte ? 
        <a href="{{ route('register') }}" class="text-agau-blue font-bold no-underline ml-[4px] relative after:content-[''] after:absolute after:bottom-[-2px] after:left-0 after:w-0 after:h-[2px] after:bg-agau-blue after:transition-all after:duration-300 hover:after:w-full">S'inscrire</a>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });
});
</script>
@endsection
