{{--
    Composant affichage d'un commentaire individuel (avec réponses imbriquées)
    Variables attendues : $commentaire, $commentableType, $commentableId, $isReply
--}}
<div
    id="comment-{{ $commentaire->id }}"
    style="
        margin-bottom: {{ $isReply ? '12px' : '20px' }};
        padding: {{ $isReply ? '14px 16px' : '20px 24px' }};
        background: {{ $commentaire->is_flagged ? '#fff9f0' : ($isReply ? '#f4f8fc' : 'white') }};
        border-radius: 10px;
        border: 1px solid {{ $commentaire->is_flagged ? '#fde68a' : ($isReply ? '#e2eaf4' : '#edf2f7') }};
        {{ $isReply ? 'margin-left: 32px;' : '' }}
    "
>
    {{-- En-tête du commentaire --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            {{-- Avatar --}}
            <div style="width:32px;height:32px;background:#1B6CA8;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.7rem;flex-shrink:0;">
                {{ strtoupper(substr($commentaire->display_name, 0, 1)) }}
            </div>
            <div>
                <span style="font-weight:700;font-size:.875rem;color:#1a202c;">{{ $commentaire->display_name }}</span>
                <span style="font-size:.75rem;color:#9ca3af;margin-left:8px;">{{ $commentaire->created_at->diffForHumans() }}</span>
                @if($commentaire->user?->alias)
                    <span style="font-size:.65rem;background:#e8f0f7;color:#1B6CA8;border-radius:4px;padding:1px 6px;margin-left:6px;font-weight:600;">alias</span>
                @endif
            </div>
        </div>

        {{-- Badges modération --}}
        <div style="display:flex;align-items:center;gap:6px;">
            @if($commentaire->is_flagged)
                <span title="{{ $commentaire->flag_reason }}" style="font-size:.65rem;background:#fef3c7;color:#92400e;border-radius:4px;padding:2px 8px;font-weight:700;">
                    🟡 Signalé
                </span>
            @endif

            @if(auth()->check() && auth()->user()->role === 'admin')
                <span style="font-size:.65rem;background:#f3e8ff;color:#7c3aed;border-radius:4px;padding:2px 8px;font-weight:700;">
                    #{{ $commentaire->id }}
                </span>
            @endif
        </div>
    </div>

    {{-- Contenu --}}
    <div style="font-size:.9rem;color:#374151;line-height:1.7;margin-bottom:14px;white-space:pre-wrap;">{{ $commentaire->contenu }}</div>

    {{-- Actions --}}
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">

        {{-- Répondre (si commentaire racine uniquement → max 2 niveaux) --}}
        @auth
            @if(! $isReply)
                <button
                    onclick="toggleReplyForm({{ $commentaire->id }})"
                    style="font-size:.75rem;color:#1B6CA8;background:none;border:none;cursor:pointer;font-weight:600;padding:0;"
                >
                    <i class="bi bi-reply-fill"></i> Répondre
                </button>
            @endif
        @endauth

        {{-- Modifier / Supprimer (propriétaire) --}}
        @auth
            @if(auth()->id() === $commentaire->user_id)
                <button
                    onclick="toggleEditForm({{ $commentaire->id }})"
                    style="font-size:.75rem;color:#4a5568;background:none;border:none;cursor:pointer;font-weight:600;padding:0;"
                >
                    <i class="bi bi-pencil"></i> Modifier
                </button>

                <form action="{{ route('comments.destroy', $commentaire) }}" method="POST" style="display:inline;"
                      onsubmit="return confirm('Confirmer la suppression de votre commentaire ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="font-size:.75rem;color:#e53e3e;background:none;border:none;cursor:pointer;font-weight:600;padding:0;">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            @endif

            {{-- Actions admin --}}
            @if(auth()->user()->role === 'admin')
                <button
                    onclick="showArchiveModal({{ $commentaire->id }})"
                    style="font-size:.75rem;color:#dc2626;background:none;border:none;cursor:pointer;font-weight:600;padding:0;margin-left:8px;"
                >
                    <i class="bi bi-archive-fill"></i> Archiver (admin)
                </button>
            @endif
        @endauth
    </div>

    {{-- Formulaire de réponse (masqué par défaut) --}}
    @auth
        @if(! $isReply)
            <div id="reply-form-{{ $commentaire->id }}" style="display:none;margin-top:16px;">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $commentaire->id }}">
                    @if($commentableType)
                        <input type="hidden" name="commentable_type" value="{{ $commentableType }}">
                        <input type="hidden" name="commentable_id" value="{{ $commentableId }}">
                    @endif
                    <textarea
                        name="contenu"
                        rows="3"
                        required
                        placeholder="Votre réponse..."
                        style="width:100%;padding:10px 14px;border:1.5px solid #1B6CA8;border-radius:8px;font-size:.875rem;font-family:inherit;resize:vertical;outline:none;box-sizing:border-box;"
                    ></textarea>
                    <div style="display:flex;gap:8px;margin-top:8px;">
                        <button type="submit" style="background:#1B6CA8;color:white;border:none;border-radius:6px;padding:7px 18px;font-size:.8rem;font-weight:700;cursor:pointer;">
                            <i class="bi bi-send-fill"></i> Répondre
                        </button>
                        <button type="button" onclick="toggleReplyForm({{ $commentaire->id }})"
                            style="background:#f1f5f9;color:#4a5568;border:none;border-radius:6px;padding:7px 18px;font-size:.8rem;font-weight:700;cursor:pointer;">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

    {{-- Formulaire de modification (masqué par défaut) --}}
    @auth
        @if(auth()->id() === $commentaire->user_id)
            <div id="edit-form-{{ $commentaire->id }}" style="display:none;margin-top:16px;">
                <form action="{{ route('comments.update', $commentaire) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <textarea
                        name="contenu"
                        rows="3"
                        required
                        style="width:100%;padding:10px 14px;border:1.5px solid #1B6CA8;border-radius:8px;font-size:.875rem;font-family:inherit;resize:vertical;outline:none;box-sizing:border-box;"
                    >{{ $commentaire->contenu }}</textarea>
                    <div style="display:flex;gap:8px;margin-top:8px;">
                        <button type="submit" style="background:#1B6CA8;color:white;border:none;border-radius:6px;padding:7px 18px;font-size:.8rem;font-weight:700;cursor:pointer;">
                            <i class="bi bi-check-lg"></i> Enregistrer
                        </button>
                        <button type="button" onclick="toggleEditForm({{ $commentaire->id }})"
                            style="background:#f1f5f9;color:#4a5568;border:none;border-radius:6px;padding:7px 18px;font-size:.8rem;font-weight:700;cursor:pointer;">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

    {{-- Réponses imbriquées (niveau 2) --}}
    @if($commentaire->replies->isNotEmpty())
        <div style="margin-top:16px;">
            @foreach($commentaire->replies as $reply)
                @include('components.comment-item', [
                    'commentaire'     => $reply,
                    'commentableType' => $commentableType,
                    'commentableId'   => $commentableId,
                    'isReply'         => true,
                ])
            @endforeach
        </div>
    @endif
</div>

{{-- Modal archivage admin (affiché par JS) --}}
@auth
    @if(auth()->user()->role === 'admin')
        <div id="archive-modal-{{ $commentaire->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
            <div style="background:white;border-radius:12px;padding:32px;max-width:480px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3);">
                <h3 style="font-size:1.1rem;font-weight:700;color:#dc2626;margin:0 0 8px;">
                    <i class="bi bi-archive-fill"></i> Archiver le commentaire #{{ $commentaire->id }}
                </h3>
                <p style="font-size:.875rem;color:#6b7280;margin:0 0 20px;">
                    Ce commentaire sera archivé (non supprimé). Action journalisée.
                </p>
                <form action="{{ route('admin.moderation.archive', $commentaire) }}" method="POST">
                    @csrf
                    <label style="font-size:.75rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">
                        Motif <span style="color:#dc2626;">*</span>
                    </label>
                    <textarea name="motif" rows="3" required placeholder="Contenu offensant, spam, harcèlement..."
                        style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.9rem;font-family:inherit;resize:vertical;outline:none;box-sizing:border-box;margin-bottom:16px;"></textarea>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" style="background:#dc2626;color:white;border:none;border-radius:8px;padding:10px 20px;font-size:.875rem;font-weight:700;cursor:pointer;flex:1;">
                            Confirmer l'archivage
                        </button>
                        <button type="button" onclick="closeArchiveModal({{ $commentaire->id }})"
                            style="background:#f1f5f9;color:#4a5568;border:none;border-radius:8px;padding:10px 20px;font-size:.875rem;font-weight:700;cursor:pointer;flex:1;">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endauth
