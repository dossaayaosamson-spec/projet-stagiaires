<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSuspension extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'motif',
        'suspended_at',
        'lifted_at',
    ];

    protected function casts(): array
    {
        return [
            'suspended_at' => 'datetime',
            'lifted_at' => 'datetime',
        ];
    }

    /** Utilisateur suspendu. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Admin ayant prononcé la suspension. */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** Indique si la suspension est toujours active. */
    public function isActive(): bool
    {
        return $this->lifted_at === null;
    }
}
