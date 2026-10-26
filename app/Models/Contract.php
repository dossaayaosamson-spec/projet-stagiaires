<?php

namespace App\Models;

use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id',
        'book_id',
        'manuscript_id',
        'type_contrat',
        'taux_redevance',
        'date_debut',
        'date_fin',
        'fichier_contrat_pdf',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'taux_redevance' => 'float',
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    public function isActive(): bool
    {
        return $this->statut === 'actif';
    }
}
