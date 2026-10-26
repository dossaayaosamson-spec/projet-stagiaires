<nav class="bg-white border-b border-agau-border px-[4%] h-[72px] flex items-center justify-between sticky top-0 z-[100]">
  <a href="{{ route('home') }}" class="no-underline flex-shrink-0">
    <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 60px" alt="AGAU Éditions">
    <p class="text-[7px] text-agau-muted font-bold mt-0.5 uppercase tracking-[2px]">Maison d'éditions</p>
  </a>

  <!-- Burger Button (Mobile) -->
  <button id="public-burger" class="md:hidden flex items-center justify-center p-2 text-agau-blue bg-transparent border-none cursor-pointer text-2xl">
    <i class="bi bi-list"></i>
  </button>

  <!-- Desktop Menu (flexible, sans scroll horizontal) -->
<!-- Desktop Menu (avec espacement suffisant) -->
<div class="hidden md:block flex-1 mx-4">
  <ul class="flex flex-wrap justify-evenly gap-x-6 lg:gap-x-6 mx-auto">
    <li><a href="{{ route('home') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('home') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Accueil</a></li>
    <li><a href="{{ route('front.catalogue') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('front.catalogue') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Nos Livres</a></li>
    <li><a href="{{ route('front.stories.index') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('front.stories.*') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Short Stories</a></li>
    <li><a href="{{ route('front.blog.index') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('front.blog.*') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Blog</a></li>
    <li><a href="{{ route('front.plans') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('front.plans') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Nos Offres</a></li>
    <li><a href="{{ route('front.auteurs.index') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('front.auteurs.*') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Nos Auteurs</a></li>
    <li class="relative group">
        <div class="cursor-pointer text-agau-muted text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-flex items-center {{ request()->routeIs('manuscripts.*') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">
            Lecthor <i class="bi bi-chevron-down ml-1.5 text-[0.7rem] pt-[2px]"></i>
        </div>
        <div class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-[320px] bg-white border border-agau-border rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 overflow-hidden transform origin-top-center group-hover:scale-100 scale-95">

            <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-l border-t border-agau-border rotate-45"></div>
            
            <div class="relative bg-white z-10">
                <a href="{{ route('manuscripts.create') }}" class="flex items-start gap-3 px-5 py-4 text-agau-text no-underline hover:bg-agau-blue/5 hover:text-agau-blue transition-colors border-b border-gray-100 group/link">
                    <div class="bg-agau-blue/10 text-agau-blue p-2 rounded-lg group-hover/link:bg-agau-blue group-hover/link:text-white transition-colors">
                        <i class="bi bi-magic text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-[0.9rem] font-bold mb-0.5">L'IA de relecture</span>
                        <span class="block text-[0.75rem] text-agau-muted font-normal leading-tight">Faire la relecture et analyser un manuscrit</span>
                    </div>
                </a>
                <a href="{{ route('manuscripts.auteur') }}" class="flex items-start gap-3 px-5 py-4 text-agau-text no-underline hover:bg-agau-blue/5 hover:text-agau-blue transition-colors group/link">
                    <div class="bg-agau-blue/10 text-agau-blue p-2 rounded-lg group-hover/link:bg-agau-blue group-hover/link:text-white transition-colors">
                        <i class="bi bi-pen text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-[0.9rem] font-bold mb-0.5">L'IA de réécriture</span>
                        <span class="block text-[0.75rem] text-agau-muted font-normal leading-tight">Faire la réécriture d'un manuscrit</span>
                    </div>
                </a>
            </div>
        </div>
    </li>
    <li><a href="{{ route('front.contact') }}" class="text-agau-muted no-underline text-[0.8rem] lg:text-[0.9rem] font-semibold tracking-[1px] uppercase transition-colors duration-200 hover:text-agau-blue py-[10px] inline-block {{ request()->routeIs('front.contact') ? 'text-agau-blue border-b-2 border-agau-blue pb-[2px]' : '' }}">Contact</a></li>
  </ul>
</div>

  <div class="hidden md:flex gap-2 lg:gap-3 items-center flex-shrink-0">
    <a href="{{ route('cart.index') }}" class="relative mr-1 p-1 text-agau-text no-underline hover:text-agau-blue transition-colors" aria-label="Voir mon panier">
      <i class="bi bi-cart3 text-[1.3rem] text-agau-muted"></i>
      @if(session('cart') && count(session('cart')) > 0)
        <span class="absolute -top-[6px] -right-[6px] bg-red-500 text-white text-[0.6rem] font-bold w-[16px] h-[16px] rounded-full flex items-center justify-center border border-white">
          {{ count(session('cart')) }}
        </span>
      @endif
    </a>
    @auth
      <a href="{{ route('dashboard') }}" class="border border-agau-blue text-agau-blue bg-transparent px-3 py-1.5 text-[0.65rem] font-bold tracking-[1px] uppercase rounded transition-all hover:bg-agau-blue hover:text-white no-underline whitespace-nowrap">Mon Compte</a>
    @else
      <a href="{{ route('login') }}" class="text-agau-blue font-bold text-[0.65rem] uppercase border border-agau-blue px-3 py-1.5 rounded tracking-[0.5px] no-underline hover:text-agau-blue-dark whitespace-nowrap">Connexion</a>
      <a href="{{ route('register') }}" class="bg-agau-blue text-white px-3 py-1.5 text-[0.65rem] font-bold tracking-[1px] uppercase rounded hover:bg-agau-blue-dark transition-all no-underline shadow-sm whitespace-nowrap">Inscription</a>
    @endauth
  </div>

  <!-- Mobile Menu Overlay & Drawer -->
  <div id="public-mobile-drawer" class="fixed inset-0 z-[200] hidden">
    <div id="public-drawer-overlay" class="absolute inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
    <div id="public-drawer-content" class="absolute right-0 top-0 h-full w-[280px] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out p-8 flex flex-col overflow-y-auto">
      <div class="flex justify-between items-center mb-8">
        <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 80px" alt="Logo">
        <button id="public-close-drawer" class="text-3xl text-agau-muted bg-transparent border-none cursor-pointer"><i class="bi bi-x"></i></button>
      </div>

      <nav class="flex flex-col gap-5">
        <a href="{{ route('home') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('home') ? 'text-agau-blue' : '' }}">Accueil</a>
        <a href="{{ route('front.catalogue') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('front.catalogue') ? 'text-agau-blue' : '' }}">Nos Livres</a>
        <a href="{{ route('front.stories.index') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('front.stories.*') ? 'text-agau-blue' : '' }}">Short Stories</a>
        <a href="{{ route('front.blog.index') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('front.blog.*') ? 'text-agau-blue' : '' }}">Blog</a>
        <a href="{{ route('front.plans') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('front.plans') ? 'text-agau-blue' : '' }}">Nos Offres</a>
        <a href="{{ route('front.auteurs.index') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('front.auteurs.*') ? 'text-agau-blue' : '' }}">Auteurs</a>
        
        <details class="group/details border-b border-gray-50">
            <summary class="text-agau-text text-[1rem] font-bold uppercase tracking-[1px] py-3 flex items-center justify-between cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                Lecthor <i class="bi bi-chevron-down text-sm transition-transform duration-200 group-open/details:rotate-180"></i>
            </summary>
            <div class="flex flex-col pl-4 pb-3 space-y-3 mt-1">
                <a href="{{ route('manuscripts.create') }}" class="flex items-start gap-3 text-agau-text no-underline hover:text-agau-blue transition-colors">
                    <div class="bg-agau-blue/10 text-agau-blue p-1.5 rounded text-sm mt-0.5">
                        <i class="bi bi-magic"></i>
                    </div>
                    <div>
                        <span class="block text-[0.85rem] font-bold mb-0.5 {{ request()->routeIs('manuscripts.create') ? 'text-agau-blue' : '' }}">L'IA de relecture</span>
                        <span class="block text-[0.7rem] text-agau-muted font-normal leading-tight">Faire la relecture et analyser un manuscrit</span>
                    </div>
                </a>
                <a href="{{ route('manuscripts.auteur') }}" class="flex items-start gap-3 text-agau-text no-underline hover:text-agau-blue transition-colors">
                    <div class="bg-agau-blue/10 text-agau-blue p-1.5 rounded text-sm mt-0.5">
                        <i class="bi bi-pen"></i>
                    </div>
                    <div>
                        <span class="block text-[0.85rem] font-bold mb-0.5 {{ request()->routeIs('manuscripts.auteur') ? 'text-agau-blue' : '' }}">L'IA d'écriture</span>
                        <span class="block text-[0.7rem] text-agau-muted font-normal leading-tight">Faire la réécriture d'un manuscrit</span>
                    </div>
                </a>
            </div>
        </details>

        <a href="{{ route('front.contact') }}" class="text-agau-text no-underline text-[1rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-3 border-b border-gray-50 {{ request()->routeIs('front.contact') ? 'text-agau-blue' : '' }}">Contact</a>
      </nav>

      <div class="mt-auto flex flex-col gap-4">
        <a href="{{ route('cart.index') }}" class="flex items-center gap-3 text-agau-text no-underline font-bold py-3 bg-agau-blue/5 rounded-lg px-4">
          <i class="bi bi-cart3 text-xl"></i>
          <span>Mon Panier ({{ session('cart') ? count(session('cart')) : 0 }})</span>
        </a>
        @auth
          <a href="{{ route('dashboard') }}" class="bg-agau-blue text-white text-center py-4 rounded-xl font-bold uppercase tracking-[1.5px] text-xs no-underline shadow-lg">Mon Compte</a>
        @else
          <div class="grid grid-cols-1 gap-2">
            <a href="{{ route('login') }}" class="text-center py-3 border border-agau-blue text-agau-blue rounded-xl font-bold uppercase tracking-[1px] text-[0.7rem] no-underline">Connexion</a>
            <a href="{{ route('register') }}" class="bg-agau-blue text-white text-center py-4 rounded-xl font-bold uppercase tracking-[1px] text-[0.7rem] no-underline">Inscription</a>
          </div>
        @endauth
      </div>
    </div>
  </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burger = document.getElementById('public-burger');
        const close = document.getElementById('public-close-drawer');
        const drawer = document.getElementById('public-mobile-drawer');
        const overlay = document.getElementById('public-drawer-overlay');
        const content = document.getElementById('public-drawer-content');

        function openDrawer() {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('opacity-100');
                content.classList.remove('translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            overlay.classList.remove('opacity-100');
            content.classList.add('translate-x-full');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }

        burger.onclick = openDrawer;
        close.onclick = closeDrawer;
        overlay.onclick = closeDrawer;
    });
</script>