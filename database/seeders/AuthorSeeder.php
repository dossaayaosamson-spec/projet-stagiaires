<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'nom' => 'Aminata Diallo',
                'initiales' => 'AD',
                'bio' => 'Auteure sénégalaise primée, connue pour ses récits poignants sur la modernité africaine et les défis de la diaspora.',
                'couleur_avatar' => '#1B6CA8',
                'nb_livres' => 3,
            ],
            [
                'nom' => 'Jean-Baptiste Konan',
                'initiales' => 'JBK',
                'bio' => 'Écrivain ivoirien dont les œuvres explorent les tensions entre tradition et modernité dans l\'Afrique contemporaine.',
                'couleur_avatar' => '#0A3050',
                'nb_livres' => 2,
            ],
            [
                'nom' => 'Fatima Ouédraogo',
                'initiales' => 'FO',
                'bio' => 'Poétesse et romancière burkinabée, ses écrits mêlent lyrisme et engagement social pour donner voix aux sans-voix.',
                'couleur_avatar' => '#2A8ACF',
                'nb_livres' => 2,
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
}
