@extends('layouts.app')

@section('title', 'Contact — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-16 px-4">
    <div class="max-w-4xl mx-auto space-y-12">
        
        <div class="text-center space-y-4">
            <h1 class="font-lora text-4xl md:text-5xl font-bold text-agau-text">Entrons en Contact</h1>
            <p class="text-agau-muted text-sm md:text-base max-w-2xl mx-auto font-medium">
                Une question sur nos parutions, une proposition de manuscrit ou une demande de partenariat ? N'hésitez pas à nous écrire, la maison AGAU Éditions vous répondra avec plaisir.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Informations de contact -->
            <div class="md:col-span-1 space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-agau-border/40 space-y-8 h-full">
                    <div>
                        <h3 class="text-xs font-bold text-agau-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="bi bi-geo" style="font-size:1.2rem;"></i>
                            Adresse
                        </h3>
                        <p class="text-sm font-bold text-agau-text leading-relaxed">
                            Adidogomé ruelle cité Mokpokpo, Lomé, 23 BP 63
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-agau-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="bi bi-envelope-at" style="font-size: 1.2rem;"></i>
                            Email
                        </h3>
                        <a href="mailto:agaueditions@gmail.com" class="text-sm font-bold text-agau-blue hover:text-agau-blue-dark transition-colors">
                            agaueditions@gmail.com
                        </a>
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-bold text-agau-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="bi bi-telephone" style="font-size: 1.2rem"></i>
                            Téléphone
                        </h3>
                        <p class="text-sm font-bold text-agau-text leading-relaxed">
                            +228 70 76 76 94
                        </p>
                    </div>

                    <div class="pt-6 border-t border-agau-border/20">
                        <p class="text-xs text-agau-muted italic">
                            Nos bureaux sont ouverts du lundi au vendredi, de 9h00 à 17h00.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="md:col-span-2">
                <div class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-agau-border/40">
                    
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 px-6 py-4 rounded-xl text-sm font-bold flex items-start gap-3 mb-8">
                            <i class="bi bi-check-circle text-green" style="font-size: 1.2rem ;"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('front.contact.send') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Nom Complet *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ama KUAME"
                                    class="w-full bg-agau-border/5 border border-agau-border rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                                @error('name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Adresse E-mail *</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="ama@example.com"
                                    class="w-full bg-agau-border/5 border border-agau-border rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                                @error('email') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="subject" class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Sujet du Message *</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required placeholder="Sujet"
                                class="w-full bg-agau-border/5 border border-agau-border rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                            @error('subject') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="message" class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Votre Message *</label>
                            <textarea name="message" id="message" rows="6" required placeholder="Votre message ici"
                                class="w-full bg-agau-border/5 border border-agau-border rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all resize-none">{{ old('message') }}</textarea>
                            @error('message') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-agau-blue text-white rounded-xl text-xs font-bold tracking-[2px] uppercase hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                                <i class="bi bi-send" style="font-size: 1.3rem"></i>
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
