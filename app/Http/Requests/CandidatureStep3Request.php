<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidatureStep3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fichier_manuscrit' => ['required', 'file', 'mimes:docx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'fichier_manuscrit.required' => 'Vous devez téléverser votre manuscrit.',
            'fichier_manuscrit.mimes' => 'Le fichier doit être au format DOCX uniquement.',
            'fichier_manuscrit.max' => 'La taille du fichier ne doit pas dépasser 10 Mo.',
        ];
    }
}
