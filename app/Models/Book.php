<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'author_id',
        'categorie',
        'resume',
        'badge',
        'format',
        'disponibilite',
        'maison_edition',
        'couleur_couverture',
        'cover_path',
        'pdf_path',
        'prix',
        'is_bestseller',
        'is_free',
        'free_until',
        'stock',
    ];

    protected $casts = [
        'is_bestseller' => 'boolean',
        'is_free' => 'boolean',
        'free_until' => 'datetime',
        'prix' => 'decimal:2',
    ];

    /**
     * URL de la couverture
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        if (Str::startsWith($this->cover_path, 'images/covers-test/')) {
            return asset($this->cover_path);
        }

        return asset('storage/'.$this->cover_path);
    }

    /**
     * Indique si le livre est un livre physique.
     */
    public function isPhysique(): bool
    {
        $disp = Str::lower($this->disponibilite ?? '');
        $fmt = Str::lower($this->format ?? '');

        if ($disp === 'physique') {
            return true;
        }

        if ($disp === 'numérique' || $disp === 'numerique' || $disp === 'immédiate') {
            return false;
        }

        return $fmt === 'physique' || $fmt === 'papier' || $fmt === 'broché' || $disp === 'les deux';
    }

    /**
     * Indique si le livre est en stock (uniquement pertinent pour les livres physiques).
     * Les livres numériques (stock null) sont toujours considérés disponibles.
     */
    public function isInStock(): bool
    {
        if (! $this->isPhysique()) {
            return true;
        }

        return $this->stock !== null && $this->stock > 0;
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function ratings()
    {
        return $this->hasMany(BookRating::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('statut', 'actif')->latest();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 🔥 Relation avec les favoris
    public function favoris()
    {
        return $this->hasMany(Favori::class, 'livre_id');
    }

    // 🔥 Relation many-to-many avec les utilisateurs via la table pivot 'favoris'
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favoris', 'livre_id', 'user_id')
            ->withTimestamps();
    }

    public function frontRouteParams(): array
    {
        return [
            'id' => $this->id,
            'slug' => Str::slug($this->titre),
        ];
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'book_user_progress')
            ->withPivot(['progress', 'status', 'last_read_at'])
            ->withTimestamps();
    }

    protected static function booted()
    {
        static::saved(function ($book) {
            Cache::flush();
        });

        static::deleted(function ($book) {
            Cache::flush();
        });
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
