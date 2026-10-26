<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favori extends Model
{
    protected $fillable = ['user_id', 'livre_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livre(): BelongsTo
    {
        // On précise la clé étrangère 'livre_id' pour être explicite
        return $this->belongsTo(Book::class, 'livre_id');
    }
}
