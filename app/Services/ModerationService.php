<?php

namespace App\Services;

use App\Models\AdminActionLog;
use App\Models\Comment;
use App\Models\User;
use App\Models\UserSuspension;
use Illuminate\Database\Eloquent\Collection;

class ModerationService
{
    /**
     * Résout un alias vers l'utilisateur réel (admin uniquement).
     * Journalise l'accès.
     */
    public function resoudreAlias(User $admin, string $alias): ?User
    {
        $cleanAlias = trim($alias);
        $utilisateur = User::where('alias', $cleanAlias)
            ->orWhereRaw('LOWER(alias) = ?', [strtolower($cleanAlias)])
            ->first();

        $this->journaliser($admin, 'resolve_alias', null, [
            'alias' => $alias,
            'user_id' => $utilisateur?->id,
            'user_email' => $utilisateur?->email,
        ]);

        return $utilisateur;
    }

    /**
     * Suspend un utilisateur avec un motif.
     */
    public function suspendreUtilisateur(User $admin, User $cible, string $motif): UserSuspension
    {
        $suspension = UserSuspension::create([
            'user_id' => $cible->id,
            'admin_id' => $admin->id,
            'motif' => $motif,
            'suspended_at' => now(),
        ]);

        $this->journaliser($admin, 'suspend_user', $cible, [
            'motif' => $motif,
            'suspension_id' => $suspension->id,
        ]);

        return $suspension;
    }

    /**
     * Lève une suspension.
     */
    public function leverSuspension(User $admin, UserSuspension $suspension): void
    {
        $suspension->update(['lifted_at' => now()]);

        $this->journaliser($admin, 'lift_suspension', $suspension->user, [
            'suspension_id' => $suspension->id,
        ]);
    }

    /**
     * Génère un résumé des discussions actives.
     *
     * @return array{
     *     top_oeuvres: Collection,
     *     top_utilisateurs: Collection,
     *     total_commentaires: int,
     *     flagges: int,
     *     blog_general: int,
     * }
     */
    public function genererRapport(): array
    {
        $topOeuvres = Comment::published()
            ->roots()
            ->whereNotNull('commentable_type')
            ->selectRaw('commentable_type, commentable_id, COUNT(*) as total')
            ->groupBy('commentable_type', 'commentable_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('commentable')
            ->get();

        $topUtilisateurs = Comment::published()
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('user')
            ->get();

        return [
            'top_oeuvres' => $topOeuvres,
            'top_utilisateurs' => $topUtilisateurs,
            'total_commentaires' => Comment::published()->count(),
            'flagges' => Comment::flagged()->count(),
            'blog_general' => Comment::published()->generalBlog()->count(),
        ];
    }

    /**
     * Recherche des commentaires selon des filtres.
     *
     * @param  array{
     *     contenu?: string,
     *     alias?: string,
     *     user_id?: int,
     *     commentable_type?: string,
     *     commentable_id?: int,
     *     statut?: string,
     *     is_flagged?: bool,
     * }  $filters
     */
    public function rechercherCommentaires(array $filters): Collection
    {
        $query = Comment::with(['user', 'commentable'])
            ->latest();

        if (! empty($filters['contenu'])) {
            $query->where('contenu', 'like', '%'.$filters['contenu'].'%');
        }

        if (! empty($filters['alias'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('alias', 'like', '%'.$filters['alias'].'%');
            });
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['commentable_type'])) {
            $query->where('commentable_type', $filters['commentable_type']);
        }

        if (! empty($filters['commentable_id'])) {
            $query->where('commentable_id', $filters['commentable_id']);
        }

        if (! empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (isset($filters['is_flagged'])) {
            $query->where('is_flagged', $filters['is_flagged']);
        }

        return $query->get();
    }

    /**
     * Journalise une action administrative.
     */
    private function journaliser(User $admin, string $action, ?User $cible, array $details = []): void
    {
        AdminActionLog::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'target_type' => $cible ? User::class : null,
            'target_id' => $cible?->id,
            'details' => $details,
        ]);
    }
}
