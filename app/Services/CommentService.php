<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CommentService
{
    /**
     * Liste de mots-clés problématiques pour la détection automatique légère.
     * Configurable via config/moderation.php si nécessaire.
     *
     * @var array<string>
     */
    private const MOTS_INTERDITS = [
        'insulte', 'haine', 'raciste', 'racisme', 'nigg', 'pd ', ' pd', 'pute',
        'connard', 'connasse', 'salope', 'batard', 'bâtard', 'merde', 'enculé',
        'fdp', 'fils de pute', 'spam', 'buy now', 'click here', 'free money',
        'arnaque', 'scam', 'casino',
    ];

    /**
     * Publie un nouveau commentaire.
     *
     * @param  array{
     *     contenu: string,
     *     alias?: string|null,
     *     commentable_type?: string|null,
     *     commentable_id?: int|null,
     *     parent_id?: int|null,
     * }  $data
     *
     * @throws ValidationException
     */
    public function publier(User $user, array $data): Comment
    {
        $this->verifierRateLimit($user);

        // Gestion de l'alias 1:1 avec le compte utilisateur
        if (array_key_exists('alias', $data)) {
            $alias = $data['alias'];
            if ($alias !== null && trim($alias) !== '') {
                $this->validerAlias($alias, $user);
                $user->update(['alias' => trim($alias)]);
            } else {
                // Si l'utilisateur vide le champ, on supprime son alias global
                $user->update(['alias' => null]);
            }
        }

        $flagReason = $this->detecterContenuProblematique($data['contenu']);

        $comment = Comment::create([
            'user_id' => $user->id,
            'commentable_type' => $data['commentable_type'] ?? null,
            'commentable_id' => $data['commentable_id'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'contenu' => $data['contenu'],
            'is_flagged' => $flagReason !== null,
            'flag_reason' => $flagReason,
            'statut' => 'publié',
        ]);

        return $comment;
    }

    /**
     * Modifie le contenu d'un commentaire (propriétaire uniquement).
     *
     * @throws ValidationException
     */
    public function modifierCommentaire(User $user, Comment $comment, string $contenu): Comment
    {
        if ($comment->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'commentaire' => ["Vous n'êtes pas autorisé à modifier ce commentaire."],
            ]);
        }

        if ($comment->statut === 'archivé') {
            throw ValidationException::withMessages([
                'commentaire' => ['Ce commentaire a été archivé et ne peut plus être modifié.'],
            ]);
        }

        $flagReason = $this->detecterContenuProblematique($contenu);

        $comment->update([
            'contenu' => $contenu,
            'is_flagged' => $flagReason !== null,
            'flag_reason' => $flagReason,
        ]);

        return $comment->fresh();
    }

    /**
     * Suppression par le propriétaire (soft-archive sans motif admin).
     */
    public function supprimerParUtilisateur(User $user, Comment $comment): void
    {
        if ($comment->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'commentaire' => ["Vous n'êtes pas autorisé à supprimer ce commentaire."],
            ]);
        }

        $comment->update([
            'statut' => 'archivé',
            'archived_at' => now(),
            'archive_reason' => 'Supprimé par l\'auteur',
        ]);
    }

    /**
     * Archivage par un administrateur avec motif obligatoire.
     */
    public function archiverParAdmin(User $admin, Comment $comment, string $motif): Comment
    {
        $comment->update([
            'statut' => 'archivé',
            'archived_at' => now(),
            'archived_by' => $admin->id,
            'archive_reason' => $motif,
        ]);

        return $comment->fresh();
    }

    /**
     * Valide un alias : unicité parmi les commentaires actifs, et
     * non-usurpation du nom d'un auteur publié ou d'un utilisateur existant.
     *
     * @throws ValidationException
     */
    public function validerAlias(string $alias, ?User $user = null): void
    {
        $aliasNormalise = mb_strtolower(trim($alias));

        // Vérifier contre les noms d'auteurs publiés
        $auteurs = Author::all();
        foreach ($auteurs as $auteur) {
            $nomAuteur = mb_strtolower(trim($auteur->nom ?? ''));
            if ($aliasNormalise === $nomAuteur) {
                throw ValidationException::withMessages([
                    'alias' => ["Cet alias ressemble au nom d'un auteur publié sur la plateforme et n'est pas autorisé."],
                ]);
            }
        }

        // Vérifier l'unicité parmi les utilisateurs (alias global)
        $query = User::where('alias', $alias);
        if ($user !== null) {
            $query->where('id', '!=', $user->id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'alias' => ['Cet alias est déjà utilisé par un autre utilisateur.'],
            ]);
        }
    }

    /**
     * Détecte les contenus problématiques via une liste de mots-clés.
     * Retourne la raison du flag ou null si le contenu est acceptable.
     */
    public function detecterContenuProblematique(string $contenu): ?string
    {
        $contenuNormalise = mb_strtolower($contenu);

        foreach (self::MOTS_INTERDITS as $mot) {
            if (str_contains($contenuNormalise, $mot)) {
                return "Contenu potentiellement problématique détecté : mot-clé « {$mot} ».";
            }
        }

        return null;
    }

    /**
     * Vérifie le rate limit anti-spam : max 5 commentaires par 2 minutes.
     *
     * @throws ValidationException
     */
    private function verifierRateLimit(User $user): void
    {
        $cle = 'comment-spam:'.$user->id;

        if (RateLimiter::tooManyAttempts($cle, maxAttempts: 5)) {
            $secondesRestantes = RateLimiter::availableIn($cle);
            throw ValidationException::withMessages([
                'rate_limit' => ["Vous publiez trop vite. Veuillez patienter {$secondesRestantes} seconde(s) avant de commenter à nouveau."],
            ]);
        }

        RateLimiter::hit($cle, decaySeconds: 120);
    }
}
