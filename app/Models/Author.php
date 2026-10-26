<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'initiales',
        'avatar',
        'bio',
        'couleur_avatar',
        'nb_livres',
        'solde_disponible',
        'total_gagne',
        'total_retire',
    ];

    protected function casts(): array
    {
        return [
            'solde_disponible' => 'float',
            'total_gagne' => 'float',
            'total_retire' => 'float',
            'nb_livres' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function frontRouteParams(): array
    {
        return [
            'id' => $this->id,
            'slug' => Str::slug($this->nom),
        ];
    }

    protected static function booted()
    {
        static::saved(function ($author) {
            Cache::flush();
        });

        static::deleted(function ($author) {
            Cache::flush();
        });
    }
}
