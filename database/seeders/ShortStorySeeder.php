<?php

namespace Database\Seeders;

use App\Models\ShortStory;
use Illuminate\Database\Seeder;

class ShortStorySeeder extends Seeder
{
    public function run(): void
    {
        $stories = [
            [
                'titre' => 'Le Dernier Baobab',
                'extrait' => 'Sous l\'ombre du dernier baobab du village, Kofi comprenait enfin que certaines racines ne meurent jamais — elles attendent simplement d\'être arrosées par le souvenir...',
                'auteur' => 'Aminata Diallo',
                'tags' => ['Nature', 'Mémoire', 'Afrique'],
                'gradient' => 'from-blue-800 to-indigo-900',
            ],
            [
                'titre' => 'La Voix du Fleuve',
                'extrait' => 'Mariam n\'avait jamais compris pourquoi sa grand-mère parlait au fleuve chaque matin, jusqu\'au jour où le fleuve lui répondit à elle aussi, dans cette langue oubliée...',
                'auteur' => 'Jean-Baptiste Konan',
                'tags' => ['Mystère', 'Traditions', 'Famille'],
                'gradient' => 'from-teal-800 to-blue-900',
            ],
            [
                'titre' => 'Éclats de Lumière',
                'extrait' => 'Dans la ville qui ne dort jamais, Aya collectionnait les moments de silence. Elle les gardait précieusement, comme des diamants bruts que personne ne pouvait lui voler...',
                'auteur' => 'Fatima Ouédraogo',
                'tags' => ['Urbain', 'Poésie', 'Solitude'],
                'gradient' => 'from-purple-800 to-blue-900',
            ],
        ];

        foreach ($stories as $story) {
            ShortStory::create($story);
        }
    }
}
