{{--
    Composant Section Commentaires
    Usage : @include('components.comments-section', ['model' => $book])
           @include('components.comments-section', ['model' => $story])
           @include('components.comments-section')  ← pour blog général
--}}

@php
    $commentableType = isset($model) ? get_class($model) : null;
    $commentableId   = isset($model) ? $model->id : null;

    $commentaires = \App\Models\Comment::published()
        ->roots()
        ->when($commentableType, fn($q) => $q->where('commentable_type', $commentableType)
                                              ->where('commentable_id', $commentableId))
        ->when(! $commentableType, fn($q) => $q->generalBlog())
        ->with(['user', 'replies' => fn($q) => $q->published()->with('user')])
        ->latest()
        ->get();

    $totalCommentaires = $commentaires->count();
@endphp

<section class="comments-section" id="commentaires" style="max-width:900px;margin:48px auto 0;padding:0 20px 80px;">

    {{-- En-tête --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;border-bottom:2px solid #e8f0f7;padding-bottom:16px;">
        <h2 style="font-family:'Lora',serif;font-size:1.5rem;color:#0F4A7A;margin:0;display:flex;align-items:center;gap:10px;">
            <i class="bi bi-chat-quote-fill" style="color:#1B6CA8;"></i>
            Discussions
            <span style="font-size:0.85rem;font-weight:600;background:#e8f0f7;color:#1B6CA8;border-radius:20px;padding:2px 12px;">{{ $totalCommentaires }}</span>
        </h2>
        @auth
            <a href="#new-comment" style="font-size:0.75rem;font-weight:700;color:#1B6CA8;text-decoration:none;border:1.5px solid #1B6CA8;border-radius:6px;padding:6px 14px;transition:all .2s;">
                <i class="bi bi-pencil-fill"></i> Commenter
            </a>
        @endauth
    </div>

    {{-- Alertes --}}
    @if($errors->has('suspension'))
        <div style="background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.875rem;font-weight:600;">
            <i class="bi bi-shield-x-fill"></i> {{ $errors->first('suspension') }}
        </div>
    @endif

    @if(session('success'))
        <div style="background:#f0fff4;border:1px solid #c6f6d5;color:#22543d;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.875rem;font-weight:600;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Liste des commentaires --}}
    @forelse($commentaires as $commentaire)
        @include('components.comment-item', [
            'commentaire'      => $commentaire,
            'commentableType'  => $commentableType,
            'commentableId'    => $commentableId,
            'isReply'          => false,
        ])
    @empty
        <div style="text-align:center;padding:48px 20px;color:#9ca3af;">
            <i class="bi bi-chat-dots" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.4;"></i>
            <p style="margin:0;font-size:.95rem;">Aucun commentaire pour l'instant. Soyez le premier à réagir !</p>
        </div>
    @endforelse

    {{-- Formulaire de publication --}}
    <div id="new-comment" style="margin-top:40px;background:#f8fbff;border:1px solid #e2eaf4;border-radius:12px;padding:28px;">
        <h3 style="font-size:1rem;font-weight:700;color:#0F4A7A;margin:0 0 20px;">
            <i class="bi bi-pencil-square" style="color:#1B6CA8;"></i>
            Laisser un commentaire
        </h3>

        @auth
            <form action="{{ route('comments.store') }}" method="POST">
                @csrf
                @if($commentableType)
                    <input type="hidden" name="commentable_type" value="{{ $commentableType }}">
                    <input type="hidden" name="commentable_id" value="{{ $commentableId }}">
                @endif

                <div style="margin-bottom:16px;">
                    <label for="alias-new" style="font-size:.75rem;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">
                        Alias (optionnel)
                    </label>
                    <input
                        type="text"
                        id="alias-new"
                        name="alias"
                        value="{{ old('alias', auth()->user()->alias) }}"
                        placeholder="Votre pseudo unique (lié à votre compte)"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1dde8;border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border .2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#1B6CA8'"
                        onblur="this.style.borderColor='#d1dde8'"
                    >
                    <p style="font-size:0.65rem;color:#718096;margin-top:4px;">L'alias est désormais lié à votre compte et s'appliquera à tous vos commentaires.</p>
                </div>

                <div style="margin-bottom:16px;">
                    <label for="contenu-new" style="font-size:.75rem;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">
                        Votre commentaire <span style="color:#e53e3e;">*</span>
                    </label>
                    <textarea
                        id="contenu-new"
                        name="contenu"
                        rows="4"
                        required
                        placeholder="Partagez votre avis, votre ressenti..."
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1dde8;border-radius:8px;font-size:.9rem;font-family:inherit;resize:vertical;outline:none;transition:border .2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#1B6CA8'"
                        onblur="this.style.borderColor='#d1dde8'"
                    >{{ old('contenu') }}</textarea>
                    @error('contenu')
                        <p style="color:#e53e3e;font-size:.8rem;margin:4px 0 0;">{{ $message }}</p>
                    @enderror
                    @error('rate_limit')
                        <p style="color:#e53e3e;font-size:.8rem;margin:4px 0 0;"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    style="background:#1B6CA8;color:white;border:none;border-radius:8px;padding:10px 24px;font-size:.85rem;font-weight:700;cursor:pointer;transition:all .2s;letter-spacing:.5px;"
                    onmouseover="this.style.background='#0F4A7A'"
                    onmouseout="this.style.background='#1B6CA8'"
                >
                    <i class="bi bi-send-fill"></i> Publier
                </button>
            </form>
        @else
            <div style="text-align:center;padding:24px;color:#6b7280;">
                <a href="{{ route('login') }}" style="color:#1B6CA8;font-weight:700;">Connectez-vous</a>
                ou
                <a href="{{ route('register') }}" style="color:#1B6CA8;font-weight:700;">créez un compte</a>
                pour participer aux discussions.
            </div>
        @endauth
    </div>
</section>
