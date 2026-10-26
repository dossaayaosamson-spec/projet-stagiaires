<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manuscript extends Model
{
    protected $table = 'manuscripts';

    protected $fillable = [
        'user_id',
        'submission_source',
        'title',
        'author_name',
        'pen_name',
        'category',
        'synopsis',
        'bio',
        'intent_note',
        'file_path',
        'portrait_path',
        'city',
        'country',
        'social_links',
        'certified_original',
        'analysis_data',
        'status',
        'admin_notes',
        'submitted_at',
        'analyzed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'analyzed_at' => 'datetime',
        'certified_original' => 'boolean',
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function scopeCandidatures($query)
    {
        return $query->where('submission_source', 'candidature_auteur');
    }

    public function scopeLecthorPublic($query)
    {
        return $query->where('submission_source', 'lecthor_public');
    }

    public function scopeAuteurContrat($query)
    {
        return $query->where('submission_source', 'auteur_contrat');
    }
}
