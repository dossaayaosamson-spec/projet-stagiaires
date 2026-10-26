@extends('layouts.auth')

@section('title', 'Inscription — AGAU Éditions')

@section('left-panel')
<div class="relative z-[2]">
    <h1 class="font-serif text-[28px] min-[900px]:text-[38px] text-white font-semibold leading-[1.2] mb-[20px] max-w-[450px]">
        Rejoignez la plus grande bibliothèque <br>
        <strong class="text-[#5BC4F5] italic font-semibold">d'œuvres africaines.</strong>
    </h1>
    <p class="text-[14px] min-[900px]:text-[15px] text-white/55 leading-[1.8] max-w-[380px] mb-[40px] font-sans font-normal">
        Créez votre compte pour explorer notre catalogue complet, lire en illimité et soutenir les auteurs africains.
    </p>

    <div class="flex flex-col gap-[20px] mb-[40px]">
        <div class="flex items-center gap-[16px]">
            <div class="w-[36px] h-[36px] rounded-full bg-[#5BC4F5]/20 flex justify-center items-center shrink-0">
                <span class="text-[#5BC4F5] text-[16px]">✓</span>
            </div>
            <div>
                <p class="text-white text-[14px] font-bold mb-[2px]">Accès à tous les ouvrages</p>
                <p class="text-white/60 text-[12px]">Romans, essais, nouvelles</p>
            </div>
        </div>
        <div class="flex items-center gap-[16px]">
            <div class="w-[36px] h-[36px] rounded-full bg-[#5BC4F5]/20 flex justify-center items-center shrink-0">
                <span class="text-[#5BC4F5] text-[16px]">✓</span>
            </div>
            <div>
                <p class="text-white text-[14px] font-bold mb-[2px]">Lecture multi-supports</p>
                <p class="text-white/60 text-[12px]">Lisez sur PC, tablette ou smartphone</p>
            </div>
        </div>
        <!-- Note: the 3rd item was missing in original HTML, adding one for visual balance -->
        <div class="flex items-center gap-[16px]">
            <div class="w-[36px] h-[36px] rounded-full bg-[#5BC4F5]/20 flex justify-center items-center shrink-0">
                <span class="text-[#5BC4F5] text-[16px]">✓</span>
            </div>
            <div>
                <p class="text-white text-[14px] font-bold mb-[2px]">Soutien aux créateurs</p>
                <p class="text-white/60 text-[12px]">Votre abonnement rémunère les auteurs</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('right-panel')
<div class="max-w-[480px] w-full mx-auto relative z-[2]">
    <div class="text-center mb-[40px]">
        <h2 class="font-serif text-[26px] min-[900px]:text-[32px] text-agau-text font-semibold m-0">Créer un compte</h2>
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

    <!-- Signup Form -->
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-[20px] mb-[20px]">
            <div>
                <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="prenom">Prénom</label>
                <div class="relative">
                    <input type="text" id="prenom" name="prenom" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] pl-[40px] pr-[16px] text-[14px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10" placeholder="Amah" required value="{{ old('prenom') }}">
                    <span class="absolute left-[14px] top-1/2 -translate-y-1/2 text-[15px] text-agau-muted select-none pointer-events-none"><i class="bi bi-person"></i></span>
                </div>
                @error('prenom')
                    <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="nom_famille">Nom</label>
                <div class="relative">
                    <input type="text" id="nom_famille" name="nom_famille" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] pl-[40px] pr-[16px] text-[14px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10" placeholder="Koue" required value="{{ old('nom_famille') }}">
                    <span class="absolute left-[14px] top-1/2 -translate-y-1/2 text-[15px] text-agau-muted select-none pointer-events-none"><i class="bi bi-person"></i></span>
                </div>
                @error('nom_famille')
                    <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-[20px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="email">Adresse email</label>
            <div class="relative">
                <input type="email" id="email" name="email" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] pl-[40px] pr-[16px] text-[14px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10" placeholder="exemple@email.com" required value="{{ old('email') }}">
                <span class="absolute left-[14px] top-1/2 -translate-y-1/2 text-[15px] text-agau-muted select-none pointer-events-none"><i class="bi bi-envelope"></i></span>
            </div>
            @error('email')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-[20px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="telephone">Numéro de téléphone</label>
            <div class="flex focus-within:ring-4 focus-within:ring-agau-blue/10 rounded-[6px] transition-all duration-200">
                <div class="relative shrink-0 border border-agau-border border-r-0 rounded-l-[6px] bg-[#fcfcfc]">
                    <select id="country" name="country" class="h-full bg-transparent border-none py-[12px] pl-[34px] pr-[24px] text-[14px] text-agau-text font-medium appearance-none outline-none cursor-pointer">
                        {{-- Pays africains prioritaires --}}
                        <optgroup label="— Afrique —">
                            <option value="tg" data-dial="+228" {{ old('country', 'tg') == 'tg' ? 'selected' : '' }}>+228 (Togo)</option>
                            <option value="bj" data-dial="+229" {{ old('country') == 'bj' ? 'selected' : '' }}>+229 (Bénin)</option>
                            <option value="ci" data-dial="+225" {{ old('country') == 'ci' ? 'selected' : '' }}>+225 (Côte d'Ivoire)</option>
                            <option value="sn" data-dial="+221" {{ old('country') == 'sn' ? 'selected' : '' }}>+221 (Sénégal)</option>
                            <option value="cm" data-dial="+237" {{ old('country') == 'cm' ? 'selected' : '' }}>+237 (Cameroun)</option>
                            <option value="ml" data-dial="+223" {{ old('country') == 'ml' ? 'selected' : '' }}>+223 (Mali)</option>
                            <option value="bf" data-dial="+226" {{ old('country') == 'bf' ? 'selected' : '' }}>+226 (Burkina Faso)</option>
                            <option value="gh" data-dial="+233" {{ old('country') == 'gh' ? 'selected' : '' }}>+233 (Ghana)</option>
                            <option value="ng" data-dial="+234" {{ old('country') == 'ng' ? 'selected' : '' }}>+234 (Nigeria)</option>
                            <option value="ma" data-dial="+212" {{ old('country') == 'ma' ? 'selected' : '' }}>+212 (Maroc)</option>
                            <option value="tn" data-dial="+216" {{ old('country') == 'tn' ? 'selected' : '' }}>+216 (Tunisie)</option>
                            <option value="dz" data-dial="+213" {{ old('country') == 'dz' ? 'selected' : '' }}>+213 (Algérie)</option>
                            <option value="eg" data-dial="+20"  {{ old('country') == 'eg' ? 'selected' : '' }}>+20  (Égypte)</option>
                            <option value="ke" data-dial="+254" {{ old('country') == 'ke' ? 'selected' : '' }}>+254 (Kenya)</option>
                            <option value="za" data-dial="+27"  {{ old('country') == 'za' ? 'selected' : '' }}>+27  (Afrique du Sud)</option>
                            <option value="cd" data-dial="+243" {{ old('country') == 'cd' ? 'selected' : '' }}>+243 (Congo RD)</option>
                            <option value="cg" data-dial="+242" {{ old('country') == 'cg' ? 'selected' : '' }}>+242 (Congo)</option>
                            <option value="gn" data-dial="+224" {{ old('country') == 'gn' ? 'selected' : '' }}>+224 (Guinée)</option>
                            <option value="ga" data-dial="+241" {{ old('country') == 'ga' ? 'selected' : '' }}>+241 (Gabon)</option>
                            <option value="ne" data-dial="+227" {{ old('country') == 'ne' ? 'selected' : '' }}>+227 (Niger)</option>
                            <option value="td" data-dial="+235" {{ old('country') == 'td' ? 'selected' : '' }}>+235 (Tchad)</option>
                            <option value="rw" data-dial="+250" {{ old('country') == 'rw' ? 'selected' : '' }}>+250 (Rwanda)</option>
                            <option value="et" data-dial="+251" {{ old('country') == 'et' ? 'selected' : '' }}>+251 (Éthiopie)</option>
                            <option value="tz" data-dial="+255" {{ old('country') == 'tz' ? 'selected' : '' }}>+255 (Tanzanie)</option>
                            <option value="mg" data-dial="+261" {{ old('country') == 'mg' ? 'selected' : '' }}>+261 (Madagascar)</option>
                            <option value="mu" data-dial="+230" {{ old('country') == 'mu' ? 'selected' : '' }}>+230 (Maurice)</option>
                        </optgroup>
                        {{-- Reste du monde --}}
                        <optgroup label="— Europe —">
                            <option value="fr" data-dial="+33"  {{ old('country') == 'fr' ? 'selected' : '' }}>+33  (France)</option>
                            <option value="be" data-dial="+32"  {{ old('country') == 'be' ? 'selected' : '' }}>+32  (Belgique)</option>
                            <option value="ch" data-dial="+41"  {{ old('country') == 'ch' ? 'selected' : '' }}>+41  (Suisse)</option>
                            <option value="lu" data-dial="+352" {{ old('country') == 'lu' ? 'selected' : '' }}>+352 (Luxembourg)</option>
                            <option value="de" data-dial="+49"  {{ old('country') == 'de' ? 'selected' : '' }}>+49  (Allemagne)</option>
                            <option value="gb" data-dial="+44"  {{ old('country') == 'gb' ? 'selected' : '' }}>+44  (Royaume-Uni)</option>
                            <option value="es" data-dial="+34"  {{ old('country') == 'es' ? 'selected' : '' }}>+34  (Espagne)</option>
                            <option value="it" data-dial="+39"  {{ old('country') == 'it' ? 'selected' : '' }}>+39  (Italie)</option>
                            <option value="pt" data-dial="+351" {{ old('country') == 'pt' ? 'selected' : '' }}>+351 (Portugal)</option>
                            <option value="nl" data-dial="+31"  {{ old('country') == 'nl' ? 'selected' : '' }}>+31  (Pays-Bas)</option>
                        </optgroup>
                        <optgroup label="— Amériques —">
                            <option value="us" data-dial="+1"   {{ old('country') == 'us' ? 'selected' : '' }}>+1   (États-Unis)</option>
                            <option value="ca" data-dial="+1"   {{ old('country') == 'ca' ? 'selected' : '' }}>+1   (Canada)</option>
                            <option value="br" data-dial="+55"  {{ old('country') == 'br' ? 'selected' : '' }}>+55  (Brésil)</option>
                            <option value="ht" data-dial="+509" {{ old('country') == 'ht' ? 'selected' : '' }}>+509 (Haïti)</option>
                        </optgroup>
                        <optgroup label="— Asie & Océanie —">
                            <option value="cn" data-dial="+86"  {{ old('country') == 'cn' ? 'selected' : '' }}>+86  (Chine)</option>
                            <option value="in" data-dial="+91"  {{ old('country') == 'in' ? 'selected' : '' }}>+91  (Inde)</option>
                            <option value="jp" data-dial="+81"  {{ old('country') == 'jp' ? 'selected' : '' }}>+81  (Japon)</option>
                            <option value="au" data-dial="+61"  {{ old('country') == 'au' ? 'selected' : '' }}>+61  (Australie)</option>
                        </optgroup>
                    </select>
                    <span class="absolute left-[10px] top-1/2 -translate-y-1/2 text-[15px] text-agau-muted pointer-events-none"><i class="bi bi-globe"></i></span>
                    <span class="absolute right-[8px] top-1/2 -translate-y-1/2 text-[10px] text-agau-muted pointer-events-none"><i class="bi bi-caret-down-fill"></i></span>
                </div>
                <input type="tel" id="telephone" name="telephone" class="flex-1 min-w-0 bg-[#fcfcfc] border border-agau-border rounded-r-[6px] py-[12px] px-[16px] text-[14px] text-agau-text outline-none focus:border-agau-blue" placeholder="90 00 00 00" required value="{{ old('telephone') }}">
            </div>
            @error('telephone')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>


        <div class="mb-[24px]">
            <label class="block text-[13px] font-bold text-agau-text mb-[8px]" for="password">Mot de passe</label>
            <div class="relative">
                <input type="password" id="password" name="password" class="w-full bg-[#fcfcfc] border border-agau-border rounded-[6px] py-[12px] pl-[40px] pr-[16px] text-[14px] text-agau-text transition-all duration-200 outline-none focus:border-agau-blue focus:ring-4 focus:ring-agau-blue/10 peer" placeholder="••••••••" required>
                <span class="absolute left-[14px] top-1/2 -translate-y-1/2 text-[15px] text-agau-muted select-none pointer-events-none"><i class="bi bi-lock"></i></span>
                <button type="button" class="toggle-password absolute right-[14px] top-1/2 -translate-y-1/2 bg-transparent border-none text-[15px] text-agau-muted cursor-pointer transition-colors duration-200 hover:text-agau-blue focus:outline-none">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            
            <!-- Password strength -->
            <div class="mt-[10px] hidden peer-focus:block">
                <div class="flex gap-[6px] mb-[6px]">
                    <div class="h-[4px] flex-1 bg-agau-border rounded-[2px] transition-colors duration-300 relative overflow-hidden"><div class="absolute inset-0 bg-[#E24B4A] w-[25%] transition-all duration-300"></div></div>
                    <div class="h-[4px] flex-1 bg-agau-border rounded-[2px] transition-colors duration-300 relative overflow-hidden"></div>
                    <div class="h-[4px] flex-1 bg-agau-border rounded-[2px] transition-colors duration-300 relative overflow-hidden"></div>
                </div>
                <p class="text-[11px] text-agau-muted m-0">Doit contenir au moins 8 caractères, une majuscule et un chiffre.</p>
            </div>
            @error('password')
                <p class="text-[#E24B4A] text-[12px] mt-[6px] font-medium">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Checks -->
        <div class="mb-[16px] flex items-start gap-[10px]">
            <input type="checkbox" id="terms" name="terms" class="mt-[3px] accent-agau-blue cursor-pointer" required>
            <label for="terms" class="text-[12.5px] text-agau-muted leading-[1.5] cursor-pointer">
                J'accepte les <a href="javascript:void(0)" onclick="openModal('modal-cgu')" class="text-agau-blue font-semibold no-underline hover:underline">conditions générales d'utilisation</a> et la <a href="javascript:void(0)" onclick="openModal('modal-privacy')" class="text-agau-blue font-semibold no-underline hover:underline">politique de confidentialité</a>.
            </label>
        </div>
        
        <div class="mb-[32px] flex items-start gap-[10px]">
            <input type="checkbox" id="newsletter" name="newsletter" class="mt-[3px] accent-agau-blue cursor-pointer">
            <label for="newsletter" class="text-[12.5px] text-agau-muted leading-[1.5] cursor-pointer">
                Je souhaite recevoir des nouvelles sur les nouveautés littéraires (optionnel).
            </label>
        </div>

        <button type="submit" class="w-full bg-agau-blue-dark text-white border-none py-[16px] text-[12.5px] font-bold tracking-[1.5px] uppercase rounded-[6px] cursor-pointer transition-all duration-200 shadow-[0_4px_14px_rgba(15,74,122,0.25)] hover:bg-[#0A3050] hover:shadow-[0_6px_20px_rgba(15,74,122,0.35)] hover:-translate-y-[2px] focus:outline-none focus:ring-4 focus:ring-agau-blue-dark/30">
            Créer mon compte
        </button>
    </form>

    <p class="text-center text-[14px] text-agau-text mt-[32px] font-medium">
        Vous avez déjà un compte ? 
        <a href="{{ route('login') }}" class="text-agau-blue font-bold no-underline ml-[4px] relative after:content-[''] after:absolute after:bottom-[-2px] after:left-0 after:w-0 after:h-[2px] after:bg-agau-blue after:transition-all after:duration-300 hover:after:w-full">Se connecter</a>
    </p>
</div>

@include('partials.legal-modals')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
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
