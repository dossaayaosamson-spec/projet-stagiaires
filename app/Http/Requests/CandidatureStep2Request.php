<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidatureStep2Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre_manuscrit' => ['required', 'string', 'max:255'],
            'genre_litteraire' => ['required', 'string', 'in:Roman,Nouvelle'],
            'resume' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $cleaned = trim(strip_tags($value));
                    if (empty($cleaned)) {
                        $fail('Le résumé est obligatoire.');

                        return;
                    }
                    $words = count(preg_split('/\s+/u', $cleaned));
                    if ($words < 150 || $words > 300) {
                        $fail("Le résumé de l'œuvre doit être compris entre 150 et 300 mots. Actuellement : {$words} mots.");
                    }
                },
            ],
            'nombre_mots' => ['required', 'integer', 'min:1'],
            'deja_publie' => ['required', 'boolean'],
            'soumis_ailleurs' => ['required', 'boolean'],
        ];
    }
}
