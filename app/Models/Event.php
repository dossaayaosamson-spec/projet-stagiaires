<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'date_evenement',
        'image_path',
        'lien_inscription',
        'is_active',
        'is_ecla',
    ];

    protected $casts = [
        'date_evenement' => 'datetime',
        'is_active' => 'boolean',
        'is_ecla' => 'boolean',
    ];

    /**
     * URL de l'image de l'événement
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/'.$this->image_path);
        }

        return null;
    }

    /**
     * Get the registration link with http:// or https:// prepended if it lacks a protocol.
     */
    public function getLienInscriptionAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return 'https://'.$value;
    }

    protected static function booted()
    {
        static::saved(function ($event) {
            Cache::flush();
        });

        static::deleted(function ($event) {
            Cache::flush();
        });
    }

    /**
     * Scope pour les événements à venir
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date_evenement', '>=', now())
            ->where('is_active', true)
            ->orderBy('date_evenement', 'asc');
    }
}
