<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    /** Affiche le blog général (liste des commentaires sans œuvre associée). */
    public function index(): View
    {
        $commentaires = Comment::published()
            ->generalBlog()
            ->roots()
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(15);

        return view('front.blog.index', compact('commentaires'));
    }

    /**
     * Publie un nouveau commentaire (sur une œuvre ou le blog général).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contenu' => ['required', 'string', 'min:3', 'max:5000'],
            'alias' => ['nullable', 'string', 'max:100'],
            'commentable_type' => ['nullable', 'string', 'in:App\\Models\\Book,App\\Models\\ShortStory'],
            'commentable_id' => ['nullable', 'integer'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        // Vérifier que parent_id n'est pas déjà une réponse (max 2 niveaux)
        if (! empty($validated['parent_id'])) {
            $parent = Comment::find($validated['parent_id']);
            if ($parent && $parent->parent_id !== null) {
                return back()->withErrors([
                    'parent_id' => 'Les réponses imbriquées sont limitées à 2 niveaux.',
                ])->withInput();
            }
        }

        $this->commentService->publier(auth()->user(), $validated);

        return back()->with('success', 'Votre commentaire a été publié.');
    }

    /**
     * Modifie un commentaire existant (propriétaire uniquement).
     */
    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'contenu' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $this->commentService->modifierCommentaire(
            auth()->user(),
            $comment,
            $validated['contenu']
        );

        return back()->with('success', 'Votre commentaire a été modifié.');
    }

    /**
     * Supprime (archive) un commentaire par son auteur.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->commentService->supprimerParUtilisateur(auth()->user(), $comment);

        return back()->with('success', 'Votre commentaire a été supprimé.');
    }
}
