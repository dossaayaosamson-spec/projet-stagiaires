<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'contenu',
        'is_flagged',
        'flag_reason',
        'statut',
        'archived_at',
        'archived_by',
        'archive_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relations
    // ----------------------------------------------------------------

    /** Utilisateur auteur du commentaire. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cible polymorphique : Book, ShortStory ou null (blog général).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Commentaire parent (thread). */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /** Réponses directes à ce commentaire. */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    /** Admin ayant archivé ce commentaire. */
    public function archiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    // ----------------------------------------------------------------
    // Accesseurs
    // ----------------------------------------------------------------

    /**
     * Nom affiché publiquement : alias si défini, sinon nom complet du compte.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user?->alias ?? ($this->user?->nom_complet ?? 'Utilisateur');
    }

    /**
     * Indique si le commentaire est un commentaire de blog général.
     */
    public function getIsGeneralBlogAttribute(): bool
    {
        return $this->commentable_type === null && $this->commentable_id === null;
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    /** Commentaires publiés (non archivés). */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('statut', 'publié');
    }

    /** Commentaires signalés automatiquement. */
    public function scopeFlagged(Builder $query): Builder
    {
        return $query->where('is_flagged', true);
    }

    /** Commentaires racines (pas de parent). */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Commentaires du blog général. */
    public function scopeGeneralBlog(Builder $query): Builder
    {
        return $query->whereNull('commentable_type')->whereNull('commentable_id');
    }
}
