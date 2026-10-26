<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidatureEcla extends Model
{
    protected $table = 'candidatures_ecla';

    protected $fillable = [
        'nom',
        'prenoms',
        'genre',
        'age',
        'nationalite',
        'pays_residence',
        'ville_residence',
        'quartier',
        'whatsapp',
        'email',
        'profession',
        'titre_manuscrit',
        'genre_litteraire',
        'resume',
        'nombre_mots',
        'deja_publie',
        'soumis_ailleurs',
        'fichier_manuscrit',
        'declaration_originalite',
        'acceptation_reglement',
        'compte_agau',
        'source_decouverte',
        'source_decouverte_autre',
        'statut',
        'notes_admin',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'nombre_mots' => 'integer',
            'deja_publie' => 'boolean',
            'soumis_ailleurs' => 'boolean',
            'declaration_originalite' => 'boolean',
            'acceptation_reglement' => 'boolean',
            'compte_agau' => 'boolean',
        ];
    }
}
