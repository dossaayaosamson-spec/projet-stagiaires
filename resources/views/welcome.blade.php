@extends('layouts.app')

@section('content')

    <section
      class="bg-agau-blue-deep min-h-[80vh] grid grid-cols-1 lg:grid-cols-[55%_45%] items-stretch overflow-hidden relative">
      <div class="px-[8%] py-[10px] lg:pl-[8%] lg:pr-[6%] flex flex-col justify-center relative z-[2]">
        <div
          class="inline-flex items-center w-fit max-w-full gap-[8px] bg-[#2B8ACF]/18 border border-[#2B8ACF]/35 text-[#7EC8F0] text-[0.55rem] md:text-[0.65rem] font-bold tracking-[1.5px] md:tracking-[2.5px] uppercase px-[12px] md:px-[16px] py-[6px] rounded-[5px] mb-[28px]">
          <span class="leading-tight text-left">Bibliothèque Numérique &amp; Maison d'Éditions</span>
        </div>
        <h1 class="font-serif text-[1.5rem] md:text-[2rem] text-white leading-[1.3] mb-[18px] font-semibold">
          La plateforme dédiée aux<br>
          <strong class="text-[#5BC4F5] italic font-semibold">grandes œuvres littéraires africaines</strong>
        </h1>
        <p class="text-[1.2rem] text-white/80 leading-[1.8] max-w-[420px] mb-[28px] font-light italic font-serif">
          Découvrez des ouvrages qui inspirent, questionnent et transforment. Romans, essais, nouvelles — tout un univers
          littéraire accessible partout, à tout moment.
        </p>

        {{-- BANDEAU CONTRAT D'ÉDITIONS --}}
        <div class="mb-[15px] bg-white/5 border border-white/10 rounded-[4px] p-[16px] max-w-[420px]">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-left">
            <div class="flex items-start gap-2.5 text-white/95">
              <i class="bi bi-file-earmark-text-fill text-[#5BC4F5] text-[1.2rem] mt-[2px]"></i>
              <div>
                <div class="text-[0.6rem] font-bold uppercase tracking-[1.5px] text-[#5BC4F5]">Contrat d'Éditions</div>
                <div class="text-[0.7rem] text-white/70 mt-[2px] font-light leading-snug">Processus Auteur 2026 disponible en ligne.</div>
              </div>
            </div>
            <a href="{{ route('front.contrat.editions') }}"
              class="inline-flex items-center justify-center gap-1.5 bg-[#5BC4F5] hover:bg-white text-agau-blue-deep hover:text-agau-blue-deep py-[8px] px-[16px] text-[0.6rem] font-bold tracking-[1.5px] uppercase rounded-[2px] transition-all duration-200 no-underline whitespace-nowrap w-fit self-start sm:self-center">
              <span>Lire</span>
              <i class="bi bi-arrow-right text-[0.7rem]"></i>
            </a>
          </div>
        </div>

        <div class="flex gap-[14px] flex-wrap">
          <a href="{{ route('front.plans') }}"
            class="bg-agau-blue-mid text-white border-none py-[14px] px-[32px] text-[0.78rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:bg-[#1B6CA8] font-sans no-underline inline-block">S'abonner</a>
          <a href="{{ route('front.catalogue') }}"
            class="bg-transparent text-white/80 border border-white/30 py-[14px] px-[32px] text-[0.78rem] font-semibold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:border-white hover:text-white font-sans no-underline inline-block">Découvrir
            les livres</a>
        </div>


        <div
          class="flex flex-wrap gap-[16px] sm:gap-[24px] md:gap-[36px] mt-[45px] md:mt-[18px] pt-[28px] md:pt-[16px] border-t border-white/10">
          <div>
            <div class="text-[1.3rem] md:text-[1.6rem] font-black text-white">+{{ $booksCount }}</div>
            <div class="text-[0.6rem] md:text-[0.7rem] text-white/80 tracking-[1px] md:tracking-[1.5px] uppercase mt-[3px]">
              Ouvrages</div>
          </div>
          <div>
            <div class="text-[1.3rem] md:text-[1.6rem] font-black text-white">
              +{{ number_format($readersCount, 0, ',', ' ') }}</div>
            <div class="text-[0.6rem] md:text-[0.7rem] text-white/80 tracking-[1px] md:tracking-[1.5px] uppercase mt-[3px]">
              Lecteurs</div>
          </div>
          <div>
            <div class="text-[1.3rem] md:text-[1.6rem] font-black text-white">{{ $categoriesCount }}</div>
            <div class="text-[0.6rem] md:text-[0.7rem] text-white/80 tracking-[1px] md:tracking-[1.5px] uppercase mt-[3px]">
              Catégories</div>
          </div>
        </div>
      </div>
      <!-- Swiper Hero Carousel -->
      <div class="hidden lg:block h-full relative overflow-hidden bg-agau-blue-deep">
        
        <div
          class="absolute inset-0 bg-gradient-to-r from-agau-blue-deep from-0% via-[#0A3050]/40 via-30% to-transparent to-50% z-20 pointer-events-none">
        </div>

        <div class="swiper heroSwiper h-full w-full">
          <div class="swiper-wrapper">
            @if(isset($slides) && $slides->count() > 0)
              @foreach($slides as $slide)
                <div class="swiper-slide relative h-full">
                    @if($slide->link_url)
                      <a href="{{ $slide->link_url }}" target="_blank" class="absolute inset-0 z-20"></a>
                    @endif
                    <div class="absolute inset-0 bg-agau-blue-deep"></div>
                    <div class="absolute inset-0 flex items-center justify-center" style="padding: 1em 0">
                        <img class="max-h-full max-w-full object-contain rounded-2xl"
                             src="{{ Storage::url($slide->image_path) }}"
                             alt="{{ $slide->title ?? 'AGAU Éditions' }}"
                             @if($loop->first) fetchpriority="high" loading="eager" decoding="sync" @else loading="lazy" @endif>
                    </div>
                    {{-- Overlay gradient titre/description --}}
                    @if($slide->title || $slide->description)
                      <div class="absolute inset-x-0 bottom-0 flex flex-col justify-end px-8 pb-8 lg:px-16 lg:pb-12 z-30 bg-gradient-to-t from-agau-blue-deep/90 via-agau-blue-deep/30 to-transparent pt-20">
                        @if($slide->title)
                          <h2 class="text-2xl lg:text-4xl font-bold font-serif mb-2 text-white">{{ $slide->title }}</h2>
                        @endif
                        @if($slide->description)
                          <p class="text-sm lg:text-base font-light text-white/90 line-clamp-2 max-w-2xl">{{ $slide->description }}</p>
                        @endif
                      </div>
                    @endif
                </div>
              @endforeach
            @endif
            
            <div class="swiper-slide relative h-full">
                <img class="absolute inset-0 w-full h-full object-cover object-center"
                     src="{{ asset('images/hero/hero-1.webp') }}"
                     alt="Bibliothèque AGAU"
                     loading="lazy"
                     decoding="sync">
                <div class="absolute inset-0 bg-agau-blue-deep/20"></div>
            </div>
            <div class="swiper-slide relative h-full">
                <img class="absolute inset-0 w-full h-full object-cover object-center"
                     src="{{ asset('images/hero/hero-2.webp') }}"
                     alt="Lecture et sérénité"
                     loading="lazy">
                <div class="absolute inset-0 bg-agau-blue-deep/20"></div>
            </div>
            <div class="swiper-slide relative h-full">
                <img class="absolute inset-0 w-full h-full object-cover object-center"
                     src="{{ asset('images/hero/hero-3.webp') }}"
                     alt="Collection littéraire"
                     loading="lazy">
                <div class="absolute inset-0 bg-agau-blue-deep/20"></div>
            </div>
          </div>
          <div class="swiper-pagination !left-auto !right-8 !bottom-8 !w-auto flex gap-2"></div>
        </div>
      </div>
    </section>

    <div class="bg-agau-blue px-[6%] h-[52px] flex items-center overflow-x-auto"
      style="-ms-overflow-style: none; scrollbar-width: none;">
      <div class="flex items-center justify-start lg:justify-center w-max lg:w-full gap-[20px] md:gap-[28px] pr-6"
        style="cursor: default;">
        <div
          class="flex items-center gap-[10px] px-[14px] border-r border-white/20 text-[0.68rem] font-bold tracking-[2px] uppercase text-white/90 whitespace-nowrap">
          Livres numériques</div>
        <div
          class="flex items-center gap-[10px] px-[14px] border-r border-white/20 text-[0.68rem] font-bold tracking-[2px] uppercase text-white/90 whitespace-nowrap">
          Accessible partout</div>
        <div
          class="flex items-center gap-[10px] px-[14px] border-r border-white/20 text-[0.68rem] font-bold tracking-[2px] uppercase text-white/90 whitespace-nowrap">
          Paiement sécurisé</div>
        <div
          class="flex items-center gap-[10px] px-[14px] border-r border-white/20 text-[0.68rem] font-bold tracking-[2px] uppercase text-white/90 whitespace-nowrap">
          Lecture illimitée</div>
        <div
          class="flex items-center gap-[10px] px-[14px] text-[0.68rem] font-bold tracking-[2px] uppercase text-white/90 whitespace-nowrap">
          Auteurs africains</div>
      </div>
    </div>

    <!-- SECTION : ÉVÉNEMENTS -->
    @if($events && $events->count() > 0)
      <section class="py-[60px] md:py-[80px] px-[5%] md:px-[8%] bg-white border-b border-agau-border/30 overflow-hidden">
        <div class="px-[5%] md:px-[8%] mb-[40px] flex justify-between items-end">
          <div>
            <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Actualités</p>
            <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0">Événements <span class="text-agau-blue">&
                Annonces</span></h2>
            <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
          </div>
          <div class="flex gap-2">
            <button
              class="event-prev bg-white border border-agau-border w-10 h-10 rounded-full flex items-center justify-center text-agau-blue hover:bg-agau-blue hover:text-white transition-all cursor-pointer">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button
              class="event-next bg-white border border-agau-border w-10 h-10 rounded-full flex items-center justify-center text-agau-blue hover:bg-agau-blue hover:text-white transition-all cursor-pointer">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>

        <div class="swiper eventSwiper px-[5%] md:px-[8%] !overflow-visible">
          <div class="swiper-wrapper">
            @foreach($events as $event)
              <div class="swiper-slide !h-auto">
                <div
                  class="bg-agau-off-white rounded-2xl border border-agau-border/40 overflow-hidden h-full flex flex-col group transition-all duration-500 hover:shadow-xl hover:shadow-agau-blue/5 hover:-translate-y-1">
                  <div class="relative aspect-[16/9] overflow-hidden">
                    <a href="{{ route('front.events.show', $event->id) }}" class="absolute inset-0 z-10"></a>
                    <img src="{{ $event->image_url }}"
                      class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      alt="{{ $event->titre }}" loading="lazy">
                    <div
                      class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-lg shadow-sm z-20">
                      <div class="text-[10px] font-black text-agau-blue uppercase tracking-widest">
                        {{ $event->date_evenement->translatedFormat('d M') }}</div>
                      <div class="text-[8px] font-bold text-agau-muted uppercase text-center">
                        {{ $event->date_evenement->format('Y') }}</div>
                    </div>
                  </div>
                  <div class="p-6 flex-1 flex flex-col relative z-20 pointer-events-none">
                    <a href="{{ route('front.events.show', $event->id) }}"
                      class="font-serif text-xl font-bold text-agau-text mb-3 group-hover:text-agau-blue transition-colors line-clamp-1 no-underline pointer-events-auto">{{ $event->titre }}</a>
                    <p class="text-sm text-agau-muted leading-relaxed line-clamp-3 mb-6">{{ $event->description }}</p>
                    <div class="mt-auto flex items-center justify-between pointer-events-auto">
                      <span class="text-[10px] font-bold text-agau-muted flex items-center gap-2 uppercase tracking-tight">
                        <i class="bi bi-clock"></i> {{ $event->date_evenement->format('H:i') }}
                      </span>
                      @if($event->is_ecla || Str::contains(strtolower($event->titre), ['ecla', 'candidature', 'manuscrit']) || Str::contains(strtolower($event->description), ['ecla', 'candidature', 'manuscrit']))
                        <a href="{{ route('candidature.step1') }}"
                          class="text-xs font-bold text-agau-blue hover:underline flex items-center gap-1 uppercase tracking-widest no-underline">
                          Postuler <i class="bi bi-pencil-square"></i>
                        </a>
                      @elseif($event->lien_inscription)
                        @auth
                          <a href="{{ $event->lien_inscription }}" target="_blank"
                            class="text-xs font-bold text-agau-blue hover:underline flex items-center gap-1 uppercase tracking-widest no-underline">
                            S'inscrire <i class="bi bi-arrow-right"></i>
                          </a>
                        @else
                          <a href="{{ route('login') }}"
                            class="text-xs font-bold text-agau-blue hover:underline flex items-center gap-1 uppercase tracking-widest no-underline">
                            Se connecter <i class="bi bi-box-arrow-in-right"></i>
                          </a>
                        @endauth
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- SECTION : Vedette & Best-Sellers -->
    <section class="py-[60px] md:py-[80px] px-[5%] md:px-[8%] bg-agau-off-white">
      <div class="flex flex-col lg:flex-row gap-12">

        <!-- Côté Gauche : A la Une -->
        <div class="lg:w-[65%]">
          <div class="mb-10">
            <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-2">Sélection editoriale</p>
            <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0">À la une <span
                class="text-agau-blue">de la journée</span></h2>
            <div class="w-[40px] h-[3px] bg-agau-blue mt-3 rounded-[2px]"></div>
          </div>

          @if($featuredBook)
            <div
              class="relative bg-white rounded-3xl border border-agau-border/40 p-8 md:p-12 shadow-sm overflow-hidden group">
              <div
                class="absolute top-0 right-0 w-64 h-64 bg-agau-blue/5 rounded-full -mr-32 -mt-32 blur-3xl transition-all duration-1000 group-hover:bg-agau-blue/10">
              </div>

              <div
                class="relative z-10 grid grid-cols-1 md:grid-cols-[240px_1fr] gap-10 items-center text-center md:text-left">
                <div class="mx-auto md:mx-0">
                  <div
                    class="w-[200px] aspect-[2/3] rounded-[4px_12px_12px_4px] relative shadow-[-12px_20px_40px_rgba(15,74,122,0.25)] transition-transform duration-500 group-hover:-translate-y-2"
                    @style(['background: linear-gradient(to bottom right, ' . $featuredBook->couleur_couverture . ', #2A8ACF)'])>
                    <div class="absolute left-0 top-0 bottom-0 w-[10px] bg-black/20 rounded-l-[4px] z-20"></div>
                    @if($featuredBook->cover_url)
                      <img src="{{ $featuredBook->cover_url }}"
                        class="absolute inset-0 w-full h-full object-cover rounded-[4px_12px_12px_4px]" loading="lazy"
                        alt="{{ $featuredBook->titre }}">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent z-10"></div>
                  </div>
                </div>

                <div>
                  <header class="mb-6">
                    <span
                      class="inline-block bg-agau-blue/10 text-agau-blue text-[0.62rem] font-black tracking-[2px] uppercase px-4 py-1.5 rounded-full mb-4">Focus
                      Editorial</span>
                    <h3
                      class="font-serif text-3xl md:text-4xl text-agau-text font-bold mb-2 group-hover:text-agau-blue transition-colors">
                      {{ $featuredBook->titre }}</h3>
                    <div class="flex items-center gap-3 justify-center md:justify-start">
                      <div class="w-8 h-[1.5px] bg-agau-blue/40"></div>
                      <p class="text-sm font-bold text-agau-blue uppercase tracking-[2px] m-0">
                        {{ $featuredBook->author->nom }}</p>
                    </div>
                  </header>

                  <p class="text-[0.95rem] text-agau-muted leading-relaxed font-serif italic mb-8 line-clamp-4">
                    "{{ $featuredBook->resume }}"
                  </p>

                  <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                    <a href="{{ route('front.livres.show', $featuredBook->frontRouteParams()) }}"
                      class="bg-agau-blue text-white px-8 py-3.5 rounded-full text-[0.7rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-lg shadow-agau-blue/20">Explorer
                      l'œuvre</a>
                    <form action="{{ route('cart.add', $featuredBook->id) }}" method="POST">
                      @csrf
                      <button type="submit"
                        class="bg-white text-agau-blue border-2 border-agau-blue px-8 py-3 rounded-full text-[0.7rem] font-bold tracking-widest uppercase cursor-pointer hover:bg-agau-blue hover:text-white transition-all">Commander</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>

        <!-- Côté Droit : Best-Sellers -->
        <div class="lg:w-[35%]">
          <div class="mb-10">
            <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-2">Les Incontournables</p>
            <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0">Best-<span
                class="text-agau-blue">Sellers</span></h2>
            <div class="w-[40px] h-[3px] bg-agau-blue mt-3 rounded-[2px]"></div>
          </div>

          <div class="space-y-4">
            @foreach($bestSellers as $bestBook)
              <a href="{{ route('front.livres.show', $bestBook->frontRouteParams()) }}" class="block no-underline group">
                <div
                  class="bg-white/40 border border-agau-border/30 rounded-2xl p-4 flex gap-4 transition-all hover:bg-white hover:shadow-md hover:border-agau-blue/20">
                  <div
                    class="w-16 aspect-[2/3] bg-agau-blue-deep rounded-lg overflow-hidden flex-shrink-0 shadow-sm transition-transform group-hover:scale-105"
                    @style(['background: ' . $bestBook->couleur_couverture])>
                    @if($bestBook->cover_url)
                      <img src="{{ $bestBook->cover_url }}" class="w-full h-full object-cover" loading="lazy">
                    @endif
                  </div>
                  <div class="flex flex-col justify-center">
                    <h3
                      class="text-sm font-bold text-agau-text mb-1 group-hover:text-agau-blue transition-colors line-clamp-1 m-0">
                      {{ $bestBook->titre }}</h3>
                    <p class="text-[10px] font-bold text-agau-muted uppercase tracking-wider mb-2">
                      {{ $bestBook->author->nom }}</p>
                    <span class="text-[10px] text-agau-blue font-bold flex items-center gap-1 uppercase">
                      Découvrir <i class="bi bi-chevron-right text-[8px]"></i>
                    </span>
                  </div>
                </div>
              </a>
            @endforeach
          </div>

          <div class="mt-8 pt-4">
            <a href="{{ route('front.catalogue', ['filter' => 'bestseller']) }}"
              class="text-[0.65rem] font-black text-agau-blue uppercase tracking-[3px] flex items-center gap-3 no-underline group hover:gap-5 transition-all">
              Voir tout le palmarès
              <div class="h-[1px] flex-1 bg-agau-blue/20"></div>
              <i class="bi bi-plus-circle text-lg"></i>
            </a>
          </div>
        </div>

      </div>
    </section>

    <!-- CATALOGUE -->
    <section class="py-[60px] md:py-[80px] px-[5%] md:px-[8%] bg-white">
      <div class="flex justify-between items-end mb-[48px]">
        <div>
          <p class="text-[0.9rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Bibliothèque complète</p>
          <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0">Tous nos <span
              class="text-agau-blue">Livres</span></h2>
          <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
        </div>
        <a href="{{ route('front.catalogue') }}"
          class="bg-transparent text-agau-blue border-[1.5px] border-agau-blue py-[10px] px-[22px] text-[0.7rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:bg-agau-blue hover:text-white no-underline inline-block">Voir
          tout le catalogue</a>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-[24px]">
        @foreach($latestBooks as $book)
          <x-book-card :book="$book" :user-rating="$userRatings->get($book->id)" />
        @endforeach
      </div>
    </section>

    <!-- SHORT STORIES -->
    <section class="py-[60px] md:py-[80px] px-[5%] md:px-[8%] bg-agau-off-white" id="short-stories">
      <div class="mb-[48px]">
        <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Format court</p>
        <div class="flex justify-between items-end">
          <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0">Short <span
              class="text-agau-blue">Stories</span></h2>
          <a href="{{ route('front.stories.index') }}"
            class="bg-transparent text-agau-blue border-[1.5px] border-agau-blue py-[10px] px-[22px] text-[0.7rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:bg-agau-blue hover:text-white no-underline inline-block">Voir
            toutes les short stories</a>
        </div>
        <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-[28px]">
        @foreach($stories as $story)
          <x-story-card :id="$story->id" :title="$story->titre" :tags="$story->tags" :excerpt="$story->extrait"
            :author="$story->auteur" :gradient="$story->gradient" :cover-path="$story->cover_path" />
        @endforeach
      </div>
    </section>

    <!-- NOS AUTEURS -->
    <section class="py-[80px] px-[5%] md:px-[8%] bg-agau-blue-deep">
      <div class="mb-[52px]">
        <div class="flex justify-between items-end">
          <h2 class="font-serif text-[2rem] text-white font-semibold m-0">Nos <span class="text-[#5BC4F5]">Auteurs</span>
          </h2>
          <a href="{{ route('front.auteurs.index') }}"
            class="bg-transparent text-[#5BC4F5] border-[1.5px] border-[#5BC4F5] py-[10px] px-[22px] text-[0.7rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:bg-[#5BC4F5] hover:text-[#0A3050] no-underline inline-block">Voir
            tous nos auteurs</a>
        </div>
        <div class="w-[40px] h-[3px] bg-[#2A8ACF] mt-[14px] rounded-[2px]"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-[32px]">
        @foreach($authors as $author)
          <x-author-card :initials="$author->initiales" :name="$author->nom" :bio="$author->bio" :count="$author->books_count"
            :color="$author->couleur_avatar" :avatar="$author->avatar" />
        @endforeach
      </div>
    </section>

    <!-- CITATION -->
    <section class="bg-agau-blue-dark py-[72px] px-[8%] text-center overflow-hidden">
      <div class="swiper quoteSwiper max-w-[800px] mx-auto">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="text-[3rem] text-[#5BC4F5]/20 mb-[4px] font-serif leading-none font-georgia">"</div>
            <p class="font-serif text-[1.45rem] text-white italic max-w-[620px] mx-auto mb-[20px] leading-[1.7]">
              Plus vous lisez, plus vous saurez de choses. Plus vous en apprenez, plus vous irez dans d'endroits.
            </p>
            <p class="text-[0.72rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0">— Dr. Seuss</p>
          </div>
          <div class="swiper-slide">
            <div class="text-[3rem] text-[#5BC4F5]/20 mb-[4px] font-serif leading-none font-georgia">"</div>
            <p class="font-serif text-[1.45rem] text-white italic max-w-[620px] mx-auto mb-[20px] leading-[1.7]">
              Un lecteur vit mille vies avant de mourir. Celui qui ne lit jamais n'en vit qu'une.
            </p>
            <p class="text-[0.72rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0">— George R.R. Martin</p>
          </div>
          <div class="swiper-slide">
            <div class="text-[3rem] text-[#5BC4F5]/20 mb-[4px] font-serif leading-none font-georgia">"</div>
            <p class="font-serif text-[1.45rem] text-white italic max-w-[620px] mx-auto mb-[20px] leading-[1.7]">
              La lecture est à l'esprit ce que l'exercice est au corps.
            </p>
            <p class="text-[0.72rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0">— Joseph Addison</p>
          </div>
          <div class="swiper-slide">
            <div class="text-[3rem] text-[#5BC4F5]/20 mb-[4px] font-serif leading-none font-georgia">"</div>
            <p class="font-serif text-[1.45rem] text-white italic max-w-[620px] mx-auto mb-[20px] leading-[1.7]">
              Une chambre sans livres est comme un corps sans âme.
            </p>
            <p class="text-[0.72rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] m-0">— Cicéron</p>
          </div>
        </div>
        <div class="swiper-pagination quote-pagination !relative !bottom-0 mt-8"></div>
      </div>
    </section>

    <!-- ABONNEMENTS -->
    <section class="py-[60px] md:py-[80px] px-[5%] md:px-[8%] bg-agau-off-white" id="offres">
      <div class="text-center">
        <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px] text-center">Nos offres</p>
        <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0 text-center">Choisissez votre <span
            class="text-agau-blue">abonnement</span></h2>
        <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] mx-auto rounded-[2px]"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[16px] mt-[52px]">
        @foreach($plans as $plan)
          <form action="{{ route('subscription.subscribe') }}" method="POST" class="h-full">
            @csrf
            <input type="hidden" name="plan" value="{{ $plan['nom'] }}">
            <x-plan-card :name="$plan['nom']" :price="number_format($plan['prix'], 0, ',', ' ')"
              :subtitle="$plan['description']" :popular="$plan['populaire']" />
          </form>
        @endforeach
      <a href="{{ route('front.plans') }}" class="text-agau-blue hover:text-agau-blue-deep m-auto">Voir toutes les offres -></a>
      </div>
    </section>

    @push('scripts')
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const swiper = new Swiper('.heroSwiper', {
            effect: 'fade',
            fadeEffect: {
              crossFade: true
            },
            autoplay: {
              delay: 5000,
              disableOnInteraction: false,
            },
            speed: 1500,
            loop: true,
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
              renderBullet: function (index, className) {
                return '<span class="' + className + ' !w-2 !h-2 !bg-white/30 !opacity-100 hover:!bg-white/60 transition-all rounded-full cursor-pointer"></span>';
              },
            },
          });

          // Event Swiper
          const eventSwiper = new Swiper('.eventSwiper', {
            slidesPerView: 1.1,
            spaceBetween: 20,
            loop: false,
            navigation: {
              nextEl: '.event-next',
              prevEl: '.event-prev',
            },
            breakpoints: {
              640: {
                slidesPerView: 2.2,
                spaceBetween: 30,
              },
              1024: {
                slidesPerView: 3.5,
                spaceBetween: 40,
              },
            }
          });

          swiper.on('slideChangeTransitionStart', function () {
            const activeSlide = swiper.slides[swiper.activeIndex];
            const img = activeSlide.querySelector('img');
            if (img) {
              img.style.transition = 'transform 6s ease-out';
              img.style.transform = 'scale(1.15)';
            }
          });

          swiper.on('slideChangeTransitionEnd', function () {
            const prevIndex = swiper.previousIndex;
            if (swiper.slides[prevIndex]) {
              const prevImg = swiper.slides[prevIndex].querySelector('img');
              if (prevImg) {
                prevImg.style.transition = 'none';
                prevImg.style.transform = 'scale(1.05)';
              }
            }
          });

          // Quote Swiper
          const quoteSwiper = new Swiper('.quoteSwiper', {
            slidesPerView: 1,
            loop: true,
            autoplay: {
              delay: 5000,
              disableOnInteraction: false,
            },
            pagination: {
              el: '.quote-pagination',
              clickable: true,
              renderBullet: function (index, className) {
                return '<span class="' + className + ' !w-2 !h-2 !bg-[#5BC4F5]/30 !opacity-100 hover:!bg-[#5BC4F5] transition-all rounded-full cursor-pointer inline-block mx-1"></span>';
              },
            },
          });
        });
      </script>
    @endpush

@endsection