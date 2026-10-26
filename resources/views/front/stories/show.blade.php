@extends('layouts.app')

@section('title', $story->titre . ' — Short Story AGAU')

@section('content')
<div class="bg-[#F4F7F9] min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
    <div class="max-w-[800px] mx-auto">
        <div class="mb-[32px]">
            <a href="{{ route('front.stories.index') }}" class="inline-flex items-center gap-[6px] text-[0.7rem] font-bold text-agau-muted tracking-[1px] uppercase no-underline hover:text-agau-blue transition-colors">
                ← Toutes les histoires
            </a>
        </div>

        <article class="bg-white border border-agau-border rounded-[12px] overflow-hidden shadow-[0_10px_50px_rgba(15,74,122,0.06)]">
            <!-- En-tête de l'histoire -->
            <div class="h-[300px] flex flex-col justify-end p-[40px] md:p-[60px] relative overflow-hidden bg-gradient-to-br {{ $story->gradient }}">
                @if($story->cover_path)
                    <img src="{{ asset('storage/' . $story->cover_path) }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 hover:scale-110" alt="{{ $story->titre }}" loading="lazy">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#0A3050]/90 to-transparent via-transparent to-[50%] z-[1]"></div>
                
                <div class="relative z-[2]">
                    <p class="text-[0.7rem] font-bold tracking-[3px] uppercase text-[#5BC4F5] mb-[12px]">
                        {{ is_array($story->tags) ? implode(' · ', $story->tags) : $story->tags }}
                    </p>
                    <h1 class="font-serif text-[2.2rem] md:text-[3rem] text-white font-semibold leading-[1.2] mb-[16px]">
                        {{ $story->titre }}
                    </h1>
                    <div class="flex items-center gap-[12px]">
                        <div class="w-[32px] h-[32px] rounded-full bg-white/20 flex items-center justify-center text-white text-[0.8rem] font-serif italic border border-white/30 text-center uppercase">
                            {{ substr($story->auteur, 0, 1) }}
                        </div>
                        <span class="text-[0.8rem] font-bold text-white/80 tracking-[1px] uppercase">{{ $story->auteur }}</span>
                    </div>
                </div>
            </div>

            <!-- Corps de l'histoire -->
            <div class="p-[40px] md:p-[80px]">
                <div class="max-w-[640px] mx-auto">
                    <p class="text-[1.1rem] whitespace-pre-wrap text-agau-muted leading-[1.8] font-serif italic mb-[48px] border-l-[4px] border-agau-blue pl-[24px]">
                        {{ $story->extrait }}
                    </p>

                    @if(!$hasAccess)
                    <div class="mt-12 p-8 bg-[#F4F7F9] border border-agau-blue/20 rounded-[12px] text-center shadow-lg relative overflow-hidden">
                        <div class="absolute -right-16 -top-16 w-32 h-32 bg-agau-blue/10 rounded-full blur-2xl"></div>
                        <div class="absolute -left-16 -bottom-16 w-32 h-32 bg-agau-blue/10 rounded-full blur-2xl"></div>
                        <i class="bi bi-lock-fill text-[2rem] text-agau-blue mb-4 block"></i>
                        <h3 class="font-serif text-[1.4rem] font-bold text-agau-text mb-2">Débloquez l'histoire complète</h3>
                        <p class="text-[0.9rem] text-agau-muted mb-6">Vous avez lu un extrait gratuit (30%). Pour continuer votre lecture et soutenir l'auteur, veuillez acheter cette histoire pour seulement 500 FCFA ou abonnez-vous.</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <form action="{{ route('front.stories.pay', $story->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-agau-blue hover:bg-agau-blue-deep text-white font-bold tracking-[1.5px] uppercase text-[0.7rem] px-8 py-3 rounded-[2px] transition-all flex items-center justify-center gap-2 w-full">
                                    <i class="bi bi-credit-card"></i> Payer 500 FCFA
                                </button>
                            </form>
                            
                            <a href="{{ route('front.plans') }}" class="bg-white hover:bg-gray-50 text-agau-blue border border-agau-blue font-bold tracking-[1.5px] uppercase text-[0.7rem] px-8 py-3 rounded-[2px] transition-all no-underline flex items-center justify-center gap-2">
                                <i class="bi bi-star-fill text-[#FACC15]"></i> S'abonner
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </article>

        <!-- Navigation Histoires suggérées -->
        <div class="mt-[60px]">
            <h3 class="font-serif text-[1.4rem] text-agau-text font-semibold mb-[24px]">À lire aussi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px]">
                <p class="text-agau-muted italic text-[0.9rem]">D'autres histoires inspirantes arrivent prochainement...</p>
            </div>
        </div>
        </div>

        {{-- Section Commentaires --}}
        @include('components.comments-section', ['model' => $story])
    </div>
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById('reply-form-' + id);
    if (form) { form.style.display = form.style.display === 'none' ? 'block' : 'none'; }
}
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    if (form) { form.style.display = form.style.display === 'none' ? 'block' : 'none'; }
}
function showArchiveModal(id) {
    const modal = document.getElementById('archive-modal-' + id);
    if (modal) { modal.style.display = 'flex'; }
}
function closeArchiveModal(id) {
    const modal = document.getElementById('archive-modal-' + id);
    if (modal) { modal.style.display = 'none'; }
}
</script>
@endsection
