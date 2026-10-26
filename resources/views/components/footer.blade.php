<footer class="bg-agau-blue-deep pt-[64px] px-[8%] pb-[28px]" style="border-top: 1px solid #0F4A7A" >
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr] gap-[48px] mb-[48px]">
    <div>
      <a href="{{ route('home') }}" class="no-underline">
        <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 100px" alt="AGAU Éditions" loading="lazy">
        <p class="text-[10px] text-white font-bold mt-1 uppercase tracking-[2px]">Maison d'éditions</p>
      </a>
      <p class="text-[1rem] text-white/80 leading-[1.8] max-w-[260px] mt-[14px] mb-0">Une bibliothèque numérique unique, accessible de partout à moindre prix. La littérature africaine pour tous.</p>
    </div>
    
    <div class="flex flex-col">
      <h4 class="text-[0.85rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0 mb-[18px]">Navigation</h4>
      <ul class="list-none m-0 p-0">
        <li class="mb-[10px]"><a href="{{ route('home') }}" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Accueil</a></li>
        <li class="mb-[10px]"><a href="{{ route('front.catalogue') }}" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Nos Livres</a></li>
        <li class="mb-[10px]"><a href="{{ route('front.stories.index') }}" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Short Stories</a></li>
        <li class="mb-[10px]"><a href="{{ route('front.plans') }}" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Nos Offres</a></li>
        <li class="mb-[10px]"><a href="{{ route('front.auteurs.index') }}" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Nos Auteurs</a></li>
      </ul>
    </div>
    
    <div class="flex flex-col">
      <h4 class="text-[0.85rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0 mb-[18px]">Informations</h4>
      <ul class="list-none m-0 p-0">
        <li class="mb-[10px]"><a href="#modal-cgu" onclick="openModal('modal-cgu'); return false;" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Conditions d'Utilisation</a></li>
        <li class="mb-[10px]"><a href="#modal-privacy" onclick="openModal('modal-privacy'); return false;" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Politique de confidentialité</a></li>
      </ul>
    </div>
    
    <div class="flex flex-col">
      <h4 class="text-[0.85rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0 mb-[18px]">Contact</h4>
      <ul class="list-none m-0 p-0">
        <li class="mb-[10px]"><a href="{{ route('front.contact') }}" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">Nous contacter</a></li>
        <li class="mb-[10px]"><i class="bi bi-facebook text-white/50" style="font-size: 1.3rem"></i>
        <a href="https://www.facebook.com/AgauEditions/" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">AGAU Editions</a></li>
        <li class="mb-[10px]"><i class="bi bi-linkedin text-white/50" style="font-size: 1.3rem"></i>
        <a href="https://www.linkedin.com/agau-editions/" class="text-white/80 text-[1rem] no-underline transition-colors duration-200 hover:text-[#5BC4F5]">AGAU Editions</a></li>
      </ul>
    </div>
  </div>
  
  <div class="border-t border-white/5 pt-[24px] flex flex-col md:flex-row justify-between items-center gap-[10px]">
    <p class="text-[0.75rem] text-white/50 m-0">© {{ date('Y') }} AGAU Éditions. Tous droits réservés.</p>
    <p class="text-[0.75rem] text-white/50 m-0">Fait avec <i class="bi bi-heart-fill"></i> pour la littérature africaine</p>
  </div>
</footer>
