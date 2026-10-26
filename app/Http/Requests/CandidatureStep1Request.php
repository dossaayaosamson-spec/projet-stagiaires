<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidatureStep1Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenoms' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'in:Masculin,Féminin'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'nationalite' => ['required', 'string', 'max:255'],
            'pays_residence' => ['required', 'string', 'max:255'],
            'ville_residence' => ['required', 'string', 'max:255'],
            'quartier' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'profession' => ['required', 'string', 'max:255'],
        ];
    }
}
