@extends('layouts.app')

@section('title', 'Processus & Contrat d\'Éditions Auteur — AGAU Éditions')

@section('content')

    {{-- ===== HERO BANNER ===== --}}
    <section class="bg-agau-blue-deep relative overflow-hidden py-[72px] px-[8%]">
        {{-- Decorative background circles --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#2A8ACF]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-[#5BC4F5]/8 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 bg-[#2B8ACF]/18 border border-[#2B8ACF]/35 text-[#7EC8F0] text-[0.6rem] font-bold tracking-[2px] uppercase px-4 py-2 rounded-[5px] mb-6">
                <i class="bi bi-file-earmark-text"></i>
                <span>Documents Officiels</span>
            </div>
            <h1 class="font-serif text-[2.2rem] md:text-[3rem] text-white leading-[1.3] font-semibold mb-5">
                Processus &amp; Contrat<br>
                <strong class="text-[#5BC4F5] italic font-semibold">d'Éditions Auteur</strong>
            </h1>
            <p class="text-white/70 text-[1rem] leading-[1.8] max-w-[560px] mx-auto mb-8 font-light">
                Découvrez les étapes du processus éditorial d'AGAU Éditions et les termes contractuels qui encadrent notre relation avec les auteurs.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}"
                   download="Contrat-Editions-Auteur-AGAU-2026.pdf"
                   id="btn-download-contract"
                   class="inline-flex items-center gap-2 bg-agau-blue-mid text-white border-none py-[14px] px-[32px] text-[0.78rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:bg-[#1B6CA8] no-underline">
                    <i class="bi bi-download text-[1rem]"></i>
                    Télécharger le PDF
                </a>
                <a href="#pdf-viewer"
                   class="inline-flex items-center gap-2 bg-transparent text-white/80 border border-white/30 py-[14px] px-[32px] text-[0.78rem] font-semibold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 hover:border-white hover:text-white no-underline">
                    <i class="bi bi-eye text-[1rem]"></i>
                    Lire en ligne
                </a>
            </div>
        </div>
    </section>

    {{-- ===== PROCESSUS STEPS ===== --}}
    <section class="bg-white py-[60px] px-[6%] md:px-[8%] border-b border-agau-border/30">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-3">Vue d'ensemble</p>
                <h2 class="font-serif text-[2rem] text-agau-text font-semibold m-0">
                    Le Processus <span class="text-agau-blue">Éditorial</span>
                </h2>
                <div class="w-[40px] h-[3px] bg-agau-blue mt-4 mx-auto rounded-[2px]"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Étape 1 --}}
                <div class="relative bg-agau-off-white border border-agau-border/30 rounded-2xl p-7 group hover:shadow-lg hover:border-agau-blue/20 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-agau-blue flex items-center justify-center text-white font-black text-sm flex-shrink-0">01</div>
                        <h3 class="font-serif text-[1.05rem] font-bold text-agau-text m-0">Soumission du Manuscrit</h3>
                    </div>
                    <p class="text-[0.85rem] text-agau-muted leading-relaxed m-0">
                        L'auteur soumet son manuscrit via notre formulaire dédié. Notre équipe éditoriale effectue une première analyse de la qualité et de la pertinence de l'œuvre.
                    </p>
                </div>

                {{-- Étape 2 --}}
                <div class="relative bg-agau-off-white border border-agau-border/30 rounded-2xl p-7 group hover:shadow-lg hover:border-agau-blue/20 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-agau-blue flex items-center justify-center text-white font-black text-sm flex-shrink-0">02</div>
                        <h3 class="font-serif text-[1.05rem] font-bold text-agau-text m-0">Évaluation & Contrat</h3>
                    </div>
                    <p class="text-[0.85rem] text-agau-muted leading-relaxed m-0">
                        Après validation éditoriale, un contrat d'éditions est proposé à l'auteur, définissant les droits, obligations, redevances et modalités de publication.
                    </p>
                </div>

                {{-- Étape 3 --}}
                <div class="relative bg-agau-off-white border border-agau-border/30 rounded-2xl p-7 group hover:shadow-lg hover:border-agau-blue/20 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-agau-blue flex items-center justify-center text-white font-black text-sm flex-shrink-0">03</div>
                        <h3 class="font-serif text-[1.05rem] font-bold text-agau-text m-0">Publication & Diffusion</h3>
                    </div>
                    <p class="text-[0.85rem] text-agau-muted leading-relaxed m-0">
                        L'œuvre est mise en forme, publiée sur notre plateforme numérique et diffusée auprès de notre communauté de lecteurs africains et internationaux.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== PDF VIEWER ===== --}}
    <section id="pdf-viewer" class="bg-agau-off-white py-[60px] px-[4%] md:px-[8%]">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <div>
                    <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-1">Document officiel</p>
                    <h2 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0">
                        Contrat d'Éditions <span class="text-agau-blue">2026</span>
                    </h2>
                    <div class="w-[40px] h-[3px] bg-agau-blue mt-3 rounded-[2px]"></div>
                </div>
                <a href="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}"
                   download="Contrat-Editions-Auteur-AGAU-2026.pdf"
                   class="inline-flex items-center gap-2 flex-shrink-0 bg-agau-blue text-white py-[12px] px-[24px] text-[0.72rem] font-bold tracking-[1.5px] uppercase rounded-[2px] transition-all hover:bg-agau-blue-dark no-underline shadow-md shadow-agau-blue/20">
                    <i class="bi bi-download"></i>
                    Télécharger (PDF)
                </a>
            </div>

            {{-- PDF embed container --}}
            <div class="bg-white rounded-2xl border border-agau-border/40 shadow-sm overflow-hidden">

                {{-- Info bar --}}
                <div class="bg-agau-blue-deep px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3 text-white/80 text-[0.72rem]">
                        <i class="bi bi-file-pdf text-[#5BC4F5] text-base"></i>
                        <span class="font-bold">Processus et Contrat d'éditions Auteur — AGAU Éditions 2026</span>
                        <span class="text-white/40">·</span>
                        <span>12 pages · PDF</span>
                    </div>
                    <a href="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}"
                       target="_blank"
                       class="text-[#5BC4F5] text-[0.68rem] font-bold uppercase tracking-[1px] no-underline hover:text-white transition-colors flex items-center gap-1">
                        <i class="bi bi-box-arrow-up-right text-xs"></i>
                        Ouvrir
                    </a>
                </div>

                {{-- PDF iframe viewer --}}
                <div class="relative" style="height: 80vh; min-height: 600px;">
                    <iframe
                        id="contract-pdf-frame"
                        src="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                        class="w-full h-full border-none"
                        title="Contrat d'Éditions Auteur AGAU 2026"
                        loading="lazy">
                        <div class="flex flex-col items-center justify-center h-full text-center p-12">
                            <i class="bi bi-file-pdf text-[3rem] text-agau-muted mb-4"></i>
                            <p class="text-agau-muted text-sm mb-6">Votre navigateur ne supporte pas l'affichage intégré des PDF.</p>
                            <a href="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}"
                               download="Contrat-Editions-Auteur-AGAU-2026.pdf"
                               class="bg-agau-blue text-white px-6 py-3 rounded-[2px] text-[0.75rem] font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all">
                                Télécharger le document
                            </a>
                        </div>
                    </iframe>
                </div>
            </div>

            {{-- Fallback for mobile / incompatible browsers --}}
            <div class="mt-6 bg-[#EBF5FF] border border-agau-blue/20 rounded-xl px-6 py-4 flex items-start gap-4">
                <i class="bi bi-info-circle-fill text-agau-blue text-xl flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-[0.82rem] font-bold text-agau-text mb-1">Problème d'affichage ?</p>
                    <p class="text-[0.78rem] text-agau-muted m-0">
                        Si le document ne s'affiche pas correctement, vous pouvez
                        <a href="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}"
                           download="Contrat-Editions-Auteur-AGAU-2026.pdf"
                           class="text-agau-blue font-bold hover:underline">télécharger le PDF</a>
                        ou
                        <a href="{{ asset('documents/contrat-editions-auteur-agau-2026.pdf') }}"
                           target="_blank"
                           class="text-agau-blue font-bold hover:underline">l'ouvrir dans un nouvel onglet</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CTA CONTACT ===== --}}
    <section class="bg-agau-blue-dark py-[60px] px-[8%] text-center">
        <div class="max-w-2xl mx-auto">
            <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] mb-3">Devenir auteur AGAU</p>
            <h2 class="font-serif text-[1.8rem] text-white font-semibold mb-4">
                Des questions sur le contrat ?
            </h2>
            <p class="text-white/70 text-[0.9rem] leading-relaxed mb-8 font-light">
                Notre équipe éditoriale est disponible pour vous accompagner dans toutes les étapes de votre parcours d'auteur chez AGAU Éditions.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('front.contact') }}"
                   class="inline-flex items-center gap-2 bg-agau-blue-mid text-white py-[14px] px-[32px] text-[0.78rem] font-bold tracking-[1.5px] uppercase rounded-[2px] transition-all hover:bg-[#1B6CA8] no-underline">
                    <i class="bi bi-envelope"></i>
                    Nous contacter
                </a>
                <a href="{{ route('manuscripts.create') }}"
                   class="inline-flex items-center gap-2 bg-transparent text-white/80 border border-white/30 py-[14px] px-[32px] text-[0.78rem] font-semibold tracking-[1.5px] uppercase rounded-[2px] transition-all hover:border-white hover:text-white no-underline">
                    <i class="bi bi-pencil-square"></i>
                    Soumettre un manuscrit
                </a>
            </div>
        </div>
    </section>

@endsection
