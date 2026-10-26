<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookUserProgress extends Model
{
    protected $table = 'book_user_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'progress',
        'status',
        'last_read_at',
    ];

    protected $casts = [
        'progress' => 'integer',
        'last_read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Marquer comme terminé automatiquement si progress = 100
     */
    public function updateProgress(int $percent): void
    {
        $this->progress = min(100, max(0, $percent));
        $this->status = $this->progress >= 100 ? 'finished' : 'reading';
        $this->last_read_at = now();
        $this->save();
    }
}
