<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Comment;
use App\Services\CommentService;
use App\Services\ModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentModerationController extends Controller
{
    public function __construct(
        private CommentService $commentService,
        private ModerationService $moderationService,
    ) {}

    /**
     * Tableau de bord de modération : tous les commentaires avec filtres.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['contenu', 'alias', 'user_id', 'commentable_type', 'statut', 'is_flagged']);

        $commentaires = Comment::with(['user', 'commentable'])
            ->when(! empty($filters['contenu']), fn ($q) => $q->where('contenu', 'like', '%'.$filters['contenu'].'%'))
            ->when(! empty($filters['alias']), fn ($q) => $q->whereHas('user', fn ($uq) => $uq->where('alias', 'like', '%'.$filters['alias'].'%')))
            ->when(! empty($filters['user_id']), fn ($q) => $q->where('user_id', $filters['user_id']))
            ->when(! empty($filters['commentable_type']), fn ($q) => $q->where('commentable_type', $filters['commentable_type']))
            ->when(! empty($filters['statut']), fn ($q) => $q->where('statut', $filters['statut']))
            ->when(isset($filters['is_flagged']) && $filters['is_flagged'] !== '', fn ($q) => $q->where('is_flagged', (bool) $filters['is_flagged']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.moderation.index', compact('commentaires', 'filters'));
    }

    /**
     * Vue des commentaires signalés automatiquement.
     */
    public function flagged(): View
    {
        $commentaires = Comment::flagged()
            ->where('statut', 'publié')
            ->with(['user', 'commentable'])
            ->latest()
            ->paginate(20);

        return view('admin.moderation.flagged', compact('commentaires'));
    }

    /**
     * Archive un commentaire avec motif obligatoire.
     */
    public function archive(Request $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'motif' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $archived = $this->commentService->archiverParAdmin(auth()->user(), $comment, $validated['motif']);

        // Journaliser l'action
        AdminActionLog::create([
            'admin_id' => auth()->id(),
            'action' => 'archive_comment',
            'target_type' => Comment::class,
            'target_id' => $comment->id,
            'details' => ['motif' => $validated['motif'], 'alias' => $comment->alias],
        ]);

        return redirect()->route('admin.moderation.index')
            ->with('success', "Commentaire #{$comment->id} archivé avec succès.");
    }

    /**
     * Résout un alias → identité réelle (admin uniquement, action journalisée).
     */
    public function resolveAlias(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'alias' => ['required', 'string', 'max:100'],
        ]);

        $utilisateur = $this->moderationService->resoudreAlias(auth()->user(), $validated['alias']);

        if (! $utilisateur) {
            return response()->json([
                'found' => false,
                'message' => "Aucun utilisateur trouvé pour l'alias « {$validated['alias']} ».",
            ]);
        }

        return response()->json([
            'found' => true,
            'alias' => $validated['alias'],
            'nom_complet' => $utilisateur->nom_complet,
            'email' => $utilisateur->email,
            'membre_depuis' => $utilisateur->created_at->format('d/m/Y'),
            'user_id' => $utilisateur->id,
        ]);
    }

    /**
     * Résumé des discussions actives.
     */
    public function rapport(): View
    {
        $rapport = $this->moderationService->genererRapport();

        return view('admin.moderation.rapport', compact('rapport'));
    }
}
