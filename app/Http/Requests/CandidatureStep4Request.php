<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidatureStep4Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'originalite_1' => ['accepted'],
            'originalite_2' => ['accepted'],
            'originalite_3' => ['accepted'],
            'originalite_4' => ['accepted'],
            'originalite_5' => ['accepted'],
            'acceptation_reglement' => ['accepted'],
            'compte_agau' => ['required', 'boolean'],
            'source_decouverte' => ['required', 'string', 'in:WhatsApp,Facebook,Instagram,TikTok,Université,Média/Presse,Site web de AGAU ÉDITIONS,Autre'],
            'source_decouverte_autre' => ['required_if:source_decouverte,Autre', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'originalite_1.accepted' => 'Vous devez certifier que l\'œuvre est entièrement originale.',
            'originalite_2.accepted' => 'Vous devez certifier que vous êtes l\'auteur(e) du manuscrit.',
            'originalite_3.accepted' => 'Vous devez certifier que l\'œuvre ne viole aucun droit d\'auteur.',
            'originalite_4.accepted' => 'Vous devez certifier que les informations fournies sont exactes.',
            'originalite_5.accepted' => 'Vous devez accepter les conditions du concours eCLA 2026.',
            'acceptation_reglement.accepted' => 'Vous devez reconnaître avoir lu et accepté le règlement officiel.',
            'compte_agau.required' => 'Veuillez préciser si vous avez un compte sur le site.',
            'source_decouverte.required' => 'Veuillez indiquer comment vous avez entendu parler du concours.',
            'source_decouverte_autre.required_if' => 'Veuillez préciser l\'autre source.',
        ];
    }
}
