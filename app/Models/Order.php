<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'statut',
        'total',
        'payment_method',
        'transaction_proof',
        'type_commande',
        'nom_destinataire',
        'telephone_livraison',
        'adresse_livraison',
        'ville_livraison',
        'pays_livraison',
        'frais_livraison',
        'nom_livreur',
        'telephone_livreur',
        'notes_livraison',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function isPhysique(): bool
    {
        return $this->type_commande === 'physique';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
