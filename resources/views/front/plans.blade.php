@extends('layouts.app')

@section('title', 'Nos Offres d\'Abonnement — AGAU Éditions')

@section('content')
    <!-- HERO OFFRES -->
    <section class="bg-agau-blue-deep pt-[20px] pb-[20px] relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#5BC4F5]/5 rounded-full -mr-[250px] -mt-[100px] blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-agau-blue-mid/10 rounded-full -ml-[150px] -mb-[100px] blur-[80px]"></div>
        
        <div class="max-w-[900px] mx-auto text-center relative z-10">
            <div class="inline-flex items-center gap-[8px] bg-[#2B8ACF]/18 border border-[#2B8ACF]/35 text-[#7EC8F0] text-[0.7rem] font-bold tracking-[3px] uppercase px-[20px] py-[8px] rounded-[5px] mb-[32px]">
                Lecture illimitée
            </div>
            <h1 class="font-serif text-[2rem] md:text-[2.5rem] text-white leading-[1.2] mb-[24px] font-semibold">
                Une <span class="text-[#5BC4F5] italic">bibliothèque entière</span><br>dans votre poche.
            </h1>
            <p class="text-[1rem] text-white/60 leading-[1.8] max-w-[650px] mx-auto mb-[0] font-light">
                Abonnez-vous pour accéder à l'intégralité de notre catalogue. Soutenez les auteurs africains et plongez dans des récits qui voyagent à travers le temps et l'espace.
            </p>
        </div>
    </section>

    <!-- POURQUOI S'ABONNER -->
    <section class="py-[100px] px-[8%] bg-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-[48px]">
            <div class="text-center group">
                <div class="w-[70px] h-[70px] bg-agau-off-white rounded-2xl flex items-center justify-center mx-auto mb-[28px] border border-agau-border/40 transition-transform group-hover:-translate-y-2">
                    <i class="bi bi-book-half text-[2rem] text-agau-blue"></i>
                </div>
                <h3 class="font-serif text-[1.4rem] font-bold text-agau-text mb-[16px]">Catalogue Complet</h3>
                <p class="text-agau-muted text-[0.95rem] leading-[1.7]">
                    Accédez à tous nos romans, essais et short stories sans aucune limite. De nouvelles œuvres sont ajoutées régulièrement.
                </p>
            </div>

            <div class="text-center group">
                <div class="w-[70px] h-[70px] bg-agau-off-white rounded-2xl flex items-center justify-center mx-auto mb-[28px] border border-agau-border/40 transition-transform group-hover:-translate-y-2">
                    <i class="bi bi-people text-[2rem] text-agau-blue"></i>
                </div>
                <h3 class="font-serif text-[1.4rem] font-bold text-agau-text mb-[16px]">Soutien aux Auteurs</h3>
                <p class="text-agau-muted text-[0.95rem] leading-[1.7]">
                    Votre abonnement contribue directement à la rémunération des auteurs et au développement de la littérature africaine.
                </p>
            </div>

            <div class="text-center group">
                <div class="w-[70px] h-[70px] bg-agau-off-white rounded-2xl flex items-center justify-center mx-auto mb-[28px] border border-agau-border/40 transition-transform group-hover:-translate-y-2">
                    <i class="bi bi-phone text-[2rem] text-agau-blue"></i>
                </div>
                <h3 class="font-serif text-[1.4rem] font-bold text-agau-text mb-[16px]">Mobilité Totale</h3>
                <p class="text-agau-muted text-[0.95rem] leading-[1.7]">
                    Lisez confortablement sur votre smartphone, tablette ou ordinateur. Votre progression est synchronisée partout.
                </p>
            </div>
        </div>
    </section>

    <!-- LES OFFRES -->
    <section class="py-[100px] px-[5%] md:px-[8%] bg-agau-off-white border-t border-agau-border/30">
        <div class="text-center mb-[64px]">
            <p class="text-[0.7rem] font-bold tracking-[4px] uppercase text-agau-blue mb-[12px]">Tarification</p>
            <h2 class="font-serif text-[2.2rem] text-agau-text font-bold m-0">Trouvez la formule <span class="text-agau-blue">qui vous ressemble</span></h2>
            <div class="w-[50px] h-[3px] bg-agau-blue mt-[18px] mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-[20px]">
            @foreach($plans as $plan)
                <div class="bg-white rounded-3xl border border-agau-border/40 overflow-hidden flex flex-col relative group transition-all duration-300 hover:shadow-2xl hover:shadow-agau-blue/30 hover:-translate-y-2 {{ $plan['populaire'] ? 'ring-2 ring-agau-blue shadow-xl' : '' }}">
                    @if($plan['populaire'])
                        <div class="bg-agau-blue text-white text-[10px] font-black uppercase tracking-widest text-center py-2">Plus Populaire</div>
                    @endif
                    
                    <div class="p-8 border-b border-agau-border/20 text-center">
                        <h4 class="text-[0.75rem] font-black uppercase tracking-[2px] text-agau-muted mb-4">{{ $plan['nom'] }}</h4>
                        <div class="flex items-baseline justify-center gap-1">
                            <span class="text-[1.8rem] font-black text-agau-text">{{ number_format($plan['prix'], 0, ',', ' ') }}</span>
                            <span class="text-[0.85rem] font-bold text-agau-muted uppercase">FCFA</span>
                        </div>
                        <p class="text-[0.8rem] italic text-agau-muted mt-3 h-[40px] flex items-center justify-center">{{ $plan['description'] }}</p>
                    </div>

                    <div class="p-8 flex-1">
                        <ul class="list-none p-0 m-0 space-y-4">
                            @foreach($plan['avantages'] as $avantage)
                                <li class="flex items-start gap-3 text-[0.88rem] text-agau-text">
                                    <i class="bi bi-check2-circle text-agau-blue text-[1rem]"></i>
                                    <span>{{ $avantage }}</span>
                                </li>
                            @endforeach

                            <li class="flex items-start gap-3 text-[0.88rem] text-agau-text">
                                <i class="bi bi-check2-circle text-agau-blue text-[1rem]"></i>
                                <span>Lecture sur tous supports</span>
                            </li>
                        </ul>
                    </div>

                    <div class="p-8 pt-0">
                        <form action="{{ route('subscription.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $plan['nom'] }}">
                            <button type="submit" class="w-full py-4 rounded-xl text-[0.75rem] font-black uppercase tracking-widest transition-all {{ $plan['populaire'] ? 'bg-agau-blue text-white shadow-lg shadow-agau-blue/20 hover:bg-agau-blue-dark' : 'bg-agau-off-white text-agau-blue border border-agau-blue/20 hover:bg-agau-blue hover:text-white' }}">
                                Choisir ce plan
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- TARIFICATIONS ÉDITION -->
    <section class="py-[100px] px-[5%] md:px-[8%] bg-white border-t border-agau-border/30">
        <div class="text-center mb-[64px]">
            <p class="text-[0.7rem] font-bold tracking-[4px] uppercase text-agau-blue mb-[12px]">Publication</p>
            <h2 class="font-serif text-[2.2rem] text-agau-text font-bold m-0">Tarifications <span class="text-agau-blue">d'Édition</span></h2>
            <div class="w-[50px] h-[3px] bg-agau-blue mt-[18px] mx-auto rounded-full"></div>
            <p class="text-agau-muted mt-6 max-w-2xl mx-auto leading-relaxed">À AGAU ÉDITIONS, nous proposons de l'édition à compte d'éditeur, compte d'auteur et compte mixte. Voici une estimation des coûts associés à l'édition d'une œuvre.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-[40px] max-w-5xl mx-auto">
            <div class="bg-agau-off-white rounded-3xl p-8 border border-agau-border/40 hover:shadow-xl hover:shadow-agau-blue/5 transition-all duration-300">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                    <i class="bi bi-pencil-square text-[1.5rem] text-agau-blue"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-agau-text mb-6">Frais d'Édition <span class="text-sm font-normal text-agau-muted block mt-1">(Exemple pour un livre < 100 pages)</span></h3>
                <ul class="space-y-4 m-0 p-0 list-none">
                    <li class="flex items-center justify-between pb-4 border-b border-agau-border/50">
                        <span class="text-agau-text font-medium text-[0.95rem]">Relecture et corrections</span>
                        <span class="font-bold text-agau-blue whitespace-nowrap">120 000 FCFA</span>
                    </li>
                    <li class="flex items-center justify-between pb-4 border-b border-agau-border/50">
                        <span class="text-agau-text font-medium text-[0.95rem]">Mise en page, couverture et maquette</span>
                        <span class="font-bold text-agau-blue whitespace-nowrap">80 000 FCFA</span>
                    </li>
                    <li class="flex items-center justify-between pt-2">
                        <span class="text-agau-text font-bold">Total minimum</span>
                        <span class="font-black text-xl text-agau-blue whitespace-nowrap">200 000 FCFA</span>
                    </li>
                </ul>
                <p class="text-xs text-agau-muted mt-4 italic">* La relecture/correction, la conception et mise en page sont totalement à la charge de l'auteur, peu importe le type d'édition choisie.</p>
            </div>
            
            <div class="bg-agau-off-white rounded-3xl p-8 border border-agau-border/40 hover:shadow-xl hover:shadow-agau-blue/5 transition-all duration-300">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                    <i class="bi bi-printer text-[1.5rem] text-agau-blue"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-agau-text mb-6">Impression & Droits d'Auteur</h3>
                <ul class="space-y-4 m-0 p-0 list-none">
                    <li class="flex items-center justify-between pb-4 border-b border-agau-border/50">
                        <span class="text-agau-text font-medium text-[0.95rem]">Coût d'impression</span>
                        <span class="font-bold text-agau-blue whitespace-nowrap">~ 15 FCFA / page</span>
                    </li>
                    <li class="flex items-center justify-between pb-4 border-b border-agau-border/50">
                        <span class="text-agau-text font-medium text-[0.95rem]">Droits d'auteur (Compte d'auteur)</span>
                        <span class="font-bold text-agau-blue whitespace-nowrap">75% HT</span>
                    </li>
                    <li class="flex items-center justify-between pt-2">
                        <span class="text-agau-text font-medium text-[0.95rem]">Droits d'auteur (Compte d'éditeur)</span>
                        <span class="font-bold text-agau-blue whitespace-nowrap">20% HT</span>
                    </li>
                </ul>
                <p class="text-xs text-agau-muted mt-4 italic">* Dans le cas d'une édition à compte d'auteur, les coûts d'impression dépendent du nombre de pages et du tirage souhaité.</p>
            </div>
        </div>
    </section>

    <!-- FAQ / INFOS PAIEMENT -->
    <section class="py-[100px] px-[8%] bg-agau-off-white border-t border-agau-border/30">
        <div class="max-w-[800px] mx-auto">
            <h2 class="font-serif text-[2rem] text-agau-text font-bold mb-[48px] text-center">Questions <span class="text-agau-blue">Fréquentes</span></h2>
            
            <div class="space-y-[32px]">
                <div class="border-b border-agau-border/40 pb-[24px]">
                    <h4 class="text-[1.1rem] font-bold text-agau-text mb-3">Comment se passe le paiement ?</h4>
                    <p class="text-agau-muted leading-relaxed">
                        Nous acceptons les paiements via Mobile Money (T-Money, Flooz) ainsi que par carte bancaire. Le processus est entièrement sécurisé par notre partenaire FedaPay.
                    </p>
                </div>
                
                <div class="border-b border-agau-border/40 pb-[24px]">
                    <h4 class="text-[1.1rem] font-bold text-agau-text mb-3">Puis-je changer de formule ?</h4>
                    <p class="text-agau-muted leading-relaxed">
                        Absolument. Vous pouvez passer à une formule supérieure à tout moment depuis votre tableau de bord dans la section "Mon Abonnement".
                    </p>
                </div>

                <div class="border-b border-agau-border/40 pb-[24px]">
                    <h4 class="text-[1.1rem] font-bold text-agau-text mb-3">Le renouvellement est-il automatique ?</h4>
                    <p class="text-agau-muted leading-relaxed">
                        Pour votre confort, l'abonnement n'est pas prélevé automatiquement. Vous recevrez une notification à l'échéance pour renouveler manuellement si vous le souhaitez.
                    </p>
                </div>
            </div>

            <div class="mt-[64px] bg-agau-blue/5 rounded-3xl p-8 border border-agau-blue/10 flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm">
                    <i class="bi bi-headset text-[1.5rem] text-agau-blue"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-[1.1rem] font-bold text-agau-text mb-1">Besoin d'aide pour choisir ?</h4>
                    <p class="text-agau-muted m-0 text-sm">Notre équipe est disponible pour vous conseiller sur la meilleure offre selon vos habitudes de lecture.</p>
                </div>
                <a href="{{ route('front.contact') }}" class="whitespace-nowrap bg-white text-agau-blue px-6 py-3 rounded-full text-[0.7rem] font-bold uppercase tracking-widest border border-agau-blue/20 hover:bg-agau-blue hover:text-white transition-all">Contactez-nous</a>
            </div>
        </div>
    </section>
@endsection
