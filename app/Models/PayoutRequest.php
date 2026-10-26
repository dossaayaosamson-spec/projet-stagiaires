<?php

namespace App\Models;

use Database\Factories\PayoutRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    /** @use HasFactory<PayoutRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id',
        'montant',
        'moyen_paiement',
        'coordonnees_paiement',
        'statut',
        'reference_transaction',
        'admin_notes',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'float',
            'validated_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopePending($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeValidated($query)
    {
        return $query->where('statut', 'valide');
    }
}
