<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ShortStory extends Model
{
    protected $fillable = [
        'titre',
        'extrait',
        'author_id',
        'auteur',
        'tags',
        'gradient',
        'cover_path',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_free' => 'boolean',
        ];
    }

    public function getCoverUrlAttribute()
    {
        if ($this->cover_path) {
            return asset('storage/'.$this->cover_path);
        }

        return null;
    }

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function frontRouteParams(): array
    {
        return [
            'id' => $this->id,
            'slug' => Str::slug($this->titre),
        ];
    }

    protected static function booted()
    {
        static::saved(function ($story) {
            Cache::flush();
        });

        static::deleted(function ($story) {
            Cache::flush();
        });
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
