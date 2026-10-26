<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom_destinataire' => ['required', 'string', 'max:255'],
            'telephone_livraison' => ['required', 'string', 'max:30'],
            'adresse_livraison' => ['required', 'string', 'max:500'],
            'ville_livraison' => ['required', 'string', 'max:100'],
            'pays_livraison' => ['required', 'string', 'max:100'],
            'notes_livraison' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_destinataire.required' => 'Le nom du destinataire est obligatoire.',
            'telephone_livraison.required' => 'Le numéro de téléphone est obligatoire.',
            'adresse_livraison.required' => 'L\'adresse de livraison est obligatoire.',
            'ville_livraison.required' => 'La ville est obligatoire.',
            'pays_livraison.required' => 'Le pays est obligatoire.',
        ];
    }
}
