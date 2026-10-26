<nav class="bg-white border-b border-agau-border px-[6%] h-[72px] flex items-center justify-between sticky top-0 z-[100]">
  <a href="{{ route('home') }}" class="no-underline">
    <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 80px" alt="AGAU Éditions">
    <p class="text-[10px] text-agau-muted font-bold mt-1 uppercase tracking-[2px]">Maison d'éditions</p>
  </a>
  
  <!-- Burger Button (Mobile) -->
  <button id="auth-burger" class="sm:hidden flex items-center justify-center p-2 text-agau-blue bg-transparent border-none cursor-pointer text-2xl">
    <i class="bi bi-list"></i>
  </button>

  <ul class="hidden sm:flex gap-[20px] list-none m-0 p-0">
    <li><a href="{{ route('home') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Accueil</a></li>
    <li><a href="{{ route('front.catalogue') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Nos Livres</a></li>
    <li><a href="{{ route('front.stories.index') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Short Stories</a></li>
    <!-- <li><a href="{{ route('front.blog.index') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Blog</a></li> -->
    <li><a href="{{ route('home') }}#plans-section" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Nos Offres</a></li>
    <li><a href="{{ route('front.auteurs.index') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Nos Auteurs</a></li>
    <li><a href="{{ route('front.contact') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Contact</a></li>
  </ul>
  
  <div class="flex items-center gap-[12px]">
    
    <a href="{{ route('dashboard') }}" class="flex items-center gap-[8px] cursor-pointer px-[12px] py-[6px] rounded-[20px] border border-agau-border bg-white transition-colors duration-200 hover:bg-agau-off-white no-underline">
      <div class="w-[30px] h-[30px] rounded-full bg-agau-blue flex items-center justify-center text-[0.72rem] font-bold text-white shrink-0">
        @if(auth()->user()->alias)
          {{ strtoupper(substr(auth()->user()->alias, 0, 1)) }}
        @else
          {{ strtoupper(substr(auth()->user()->prenom ?? 'U', 0, 1) . substr(auth()->user()->nom_famille ?? 'A', 0, 1)) }}
        @endif
      </div>
      <div class="hidden md:block">
        <div class="text-[0.72rem] font-bold text-agau-text leading-tight">{{ auth()->user()->alias ?? auth()->user()->prenom ?? 'Utilisateur' }}</div>
        <div class="text-[0.58rem] text-agau-blue font-semibold tracking-[1px] uppercase leading-tight">{{ strtolower(auth()->user()->role) === 'auteur' ? 'Auteur' : 'Lecteur' }}</div>
      </div>
      <span class="text-[10px] text-agau-muted ml-[2px]">▾</span>
    </a>
    
    <form method="POST" action="{{ route('logout') }}" class="m-0 hidden sm:block">
        @csrf
        <button type="submit" class="bg-transparent border border-agau-border text-agau-muted px-[14px] py-[7px] text-[0.65rem] font-bold tracking-[1px] uppercase cursor-pointer rounded-[4px] transition-all duration-200 font-sans hover:border-[#E24B4A] hover:text-[#E24B4A]">Déconnexion</button>
    </form>
  </div>

  <!-- Mobile Menu Overlay & Drawer -->
  <div id="auth-mobile-drawer" class="fixed inset-0 z-[200] hidden">
      <div id="auth-drawer-overlay" class="absolute inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
      <div id="auth-drawer-content" class="absolute right-0 top-0 h-full w-[280px] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out p-8 flex flex-col">
          <div class="flex justify-between items-center mb-10">
              <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 80px" alt="Logo">
              <button id="auth-close-drawer" class="text-3xl text-agau-muted bg-transparent border-none cursor-pointer"><i class="bi bi-x"></i></button>
          </div>

          <div class="flex items-center gap-3 mb-8 p-4 bg-agau-blue/5 rounded-xl border border-agau-blue/10">
              <div class="w-[45px] h-[45px] rounded-full bg-agau-blue flex items-center justify-center text-[1rem] font-bold text-white shadow-sm">
                  {{ strtoupper(substr(auth()->user()->prenom ?? 'U', 0, 1) . substr(auth()->user()->nom_famille ?? 'A', 0, 1)) }}
              </div>
              <div>
                  <div class="text-[0.9rem] font-bold text-agau-text">{{ auth()->user()->prenom }} {{ auth()->user()->nom_famille }}</div>
                  <div class="text-[0.65rem] text-agau-blue font-black uppercase tracking-[1px]">Tableau de bord</div>
              </div>
          </div>

          <nav class="flex flex-col gap-4">
              <a href="{{ route('home') }}" class="text-agau-text no-underline text-[0.95rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-2 border-b border-gray-50 {{ request()->routeIs('home') ? 'text-agau-blue' : '' }}">Accueil</a>
              <a href="{{ route('front.catalogue') }}" class="text-agau-text no-underline text-[0.95rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-2 border-b border-gray-50">Nos Livres</a>
              <a href="{{ route('front.stories.index') }}" class="text-agau-text no-underline text-[0.95rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-2 border-b border-gray-50">Short Stories</a>
              <!-- <a href="{{ route('front.blog.index') }}" class="text-agau-text no-underline text-[0.95rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-2 border-b border-gray-50">Blog</a> -->
              <a href="{{ route('dashboard') }}" class="text-agau-text no-underline text-[0.95rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-2 border-b border-gray-50 {{ request()->routeIs('dashboard') ? 'text-agau-blue' : '' }}">Mon Espace</a>
              <a href="{{ route('client.orders') }}" class="text-agau-text no-underline text-[0.95rem] font-bold uppercase tracking-[1px] hover:text-agau-blue py-2 border-b border-gray-50">Mes Commandes</a>
          </nav>

          <div class="mt-auto pt-8">
              <form method="POST" action="{{ route('logout') }}" class="m-0">
                  @csrf
                  <button type="submit" class="w-full bg-agau-off-white border border-agau-border text-agau-muted py-3 rounded-xl font-bold uppercase tracking-[1px] text-xs hover:text-red-500 hover:border-red-500/30 transition-all cursor-pointer">Déconnexion</button>
              </form>
          </div>
      </div>
  </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burger = document.getElementById('auth-burger');
        const close = document.getElementById('auth-close-drawer');
        const drawer = document.getElementById('auth-mobile-drawer');
        const overlay = document.getElementById('auth-drawer-overlay');
        const content = document.getElementById('auth-drawer-content');

        if (burger && close && drawer && overlay && content) {
            burger.onclick = function() {
                drawer.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                    content.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            };

            const closeMenu = function() {
                overlay.classList.remove('opacity-100');
                content.classList.add('translate-x-full');
                setTimeout(() => {
                    drawer.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            };

            close.onclick = closeMenu;
            overlay.onclick = closeMenu;
        }
    });
</script>
