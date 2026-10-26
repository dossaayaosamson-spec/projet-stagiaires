@extends('layouts.app')

@section('title', $event->titre . ' — Événement AGAU Éditions')

@section('content')
    <div class="bg-[#F4F7F9] min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
        <div class="max-w-[800px] mx-auto">
            <div class="mb-[32px]">
                <a href="{{ route('home') }}#evenements"
                    class="inline-flex items-center gap-[6px] text-[0.7rem] font-bold text-agau-muted tracking-[1px] uppercase no-underline hover:text-agau-blue transition-colors">
                    ← Retour à l'accueil
                </a>
            </div>

            <article
                class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-[0_10px_50px_rgba(15,74,122,0.06)]">
                <!-- En-tête de l'événement -->
                <div
                    class="h-[300px] md:h-[400px] flex flex-col justify-end p-[40px] md:p-[60px] relative overflow-hidden bg-agau-blue-deep">
                    @if($event->image_url)
                        <img src="{{ $event->image_url }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                            alt="{{ $event->titre }}" loading="lazy">
                    @endif
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#0A3050]/90 to-transparent via-transparent to-[50%] z-[1]">
                    </div>

                    <div class="relative z-[2]">
                        <p class="text-[0.8rem] font-bold tracking-[3px] w-1/3 bg-white/40 backdrop-blur p-3 rounded-lg shadow-sm uppercase text-agau-blue mb-[12px]">
                            {{ $event->date_evenement->translatedFormat('d F Y à H:i') }}
                        </p>
                        <h1
                            class="font-serif text-[2.2rem] md:text-[3rem] text-white font-semibold leading-[1.2] mb-[16px]">
                            {{ $event->titre }}
                        </h1>
                    </div>
                </div>

                <!-- Corps de l'événement -->
                <div class="p-[40px] md:p-[80px]">
                    <div class="max-w-[640px] mx-auto">
                        <div
                            class="prose prose-lg max-w-none text-agau-text leading-[2] font-serif text-[1.15rem] space-y-[28px] whitespace-pre-wrap">
                            {!! e($event->description) !!}
                        </div>

                        @if($event->lien_inscription)
                            <div class="mt-[48px] pt-[48px] border-t border-agau-border flex flex-col items-center text-center">
                                <p class="text-[0.95rem] font-bold text-agau-text mb-[16px]">Participer à cet événement</p>

                                @auth
                                    <a href="{{ $event->lien_inscription }}" target="_blank"
                                        class="inline-flex bg-agau-blue text-white px-[32px] py-[14px] rounded-full text-[0.8rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-lg shadow-agau-blue/20">
                                        S'inscrire maintenant
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex bg-agau-blue text-white px-[32px] py-[14px] rounded-full text-[0.8rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-lg shadow-agau-blue/20">
                                        Se connecter pour s'inscrire
                                    </a>
                                    <p class="text-[0.8rem] text-agau-text/60 mt-[12px]">
                                        Vous devez être connecté pour participer à cet événement.
                                    </p>
                                @endauth
                            </div>
                        @endif

                        @if($event->is_ecla || Str::contains(strtolower($event->titre), ['ecla', 'candidature', 'manuscrit']) || Str::contains(strtolower($event->description), ['ecla', 'candidature', 'manuscrit']))
                            <div class="mt-[48px] p-[32px] bg-agau-blue/5 border border-agau-blue/20 rounded-[12px] text-center">
                                <h3 class="font-serif text-[1.3rem] text-agau-text font-bold mb-[8px]">Appel à Manuscrits ECLA</h3>
                                <p class="text-[0.9rem] text-agau-muted mb-[20px] leading-[1.6]">
                                    Cet événement est lié au concours de manuscrits ECLA. Vous devez posséder un compte pour déposer votre dossier et suivre l'avancement de votre candidature.
                                </p>
                                @auth
                                    <a href="{{ route('candidature.step1') }}"
                                        class="inline-flex bg-agau-blue text-white px-[28px] py-[12px] rounded-full text-[0.75rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-md shadow-agau-blue/10">
                                        Déposer ma candidature
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex bg-agau-blue text-white px-[28px] py-[12px] rounded-full text-[0.75rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-md shadow-agau-blue/10">
                                        Se connecter pour postuler
                                    </a>
                                    <p class="text-[0.8rem] text-agau-text/60 mt-[12px]">
                                        L'inscription est simple et gratuite.
                                    </p>
                                @endauth
                            </div>
                        @endif

                        <div class="mt-[60px] pt-[40px] border-t border-agau-border flex flex-col items-center text-center">
                            <p class="text-[0.85rem] text-agau-muted font-serif italic mb-[24px]">Partagez cet événement</p>
                            <div class="flex gap-[16px]">
                                <div
                                    class="w-[40px] h-[40px] rounded-full border border-agau-border flex items-center justify-center text-agau-muted hover:border-agau-blue hover:text-agau-blue cursor-pointer transition-all">
                                    f</div>
                                <div
                                    class="w-[40px] h-[40px] rounded-full border border-agau-border flex items-center justify-center text-agau-muted hover:border-agau-blue hover:text-agau-blue cursor-pointer transition-all">
                                    t</div>
                                <div
                                    class="w-[40px] h-[40px] rounded-full border border-agau-border flex items-center justify-center text-agau-muted hover:border-agau-blue hover:text-agau-blue cursor-pointer transition-all">
                                    in</div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection