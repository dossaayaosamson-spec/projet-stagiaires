<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère les IDs auteurs insérés par AuthorSeeder
        $aminata = Author::where('initiales', 'AD')->first();
        $jbk = Author::where('initiales', 'JBK')->first();
        $fatima = Author::where('initiales', 'FO')->first();

        $books = [
            // ─── Livre en vedette ───────────────────────────────────────────
            [
                'titre' => 'Les Échos du Sahel',
                'author_id' => $aminata?->id ?? 1,
                'categorie' => 'Roman',
                'resume' => 'Une épopée captivante qui traverse les paysages arides et fertiles du Sahel africain, racontant l\'histoire de familles dont les destins sont entrelacés par la terre, l\'eau et les traditions ancestrales.',
                'badge' => 'Coup de cœur',
                'couleur_couverture' => 'from-blue-900 to-indigo-700',
                'prix' => 12.99,
                'is_bestseller' => false,
            ],
            // ─── Best-sellers (3) ────────────────────────────────────────────
            [
                'titre' => 'Racines Profondes',
                'author_id' => $aminata?->id ?? 1,
                'categorie' => 'Littérature africaine',
                'resume' => 'Un roman puissant sur l\'identité et l\'appartenance.',
                'badge' => 'Bestseller',
                'couleur_couverture' => '#1a365d',
                'prix' => 15.99,
                'is_bestseller' => true,
            ],
            [
                'titre' => 'L\'Aube de Demain',
                'author_id' => $jbk?->id ?? 2,
                'categorie' => 'Contemporain',
                'resume' => 'Entre espoir et résilience, une jeunesse africaine réinvente son avenir.',
                'badge' => 'Coup de cœur',
                'couleur_couverture' => '#1a3a2a',
                'prix' => 13.99,
                'is_bestseller' => true,
            ],
            [
                'titre' => 'Mémoires du Fleuve',
                'author_id' => $fatima?->id ?? 3,
                'categorie' => 'Poésie',
                'resume' => 'Un recueil poétique qui célèbre les fleuves d\'Afrique comme mémoire vivante.',
                'badge' => 'Nouveau',
                'couleur_couverture' => '#3d1a00',
                'prix' => 10.99,
                'is_bestseller' => true,
            ],
            // ─── Catalogue complet (6 autres) ────────────────────────────────
            [
                'titre' => 'Savane en Fleurs',
                'author_id' => $fatima?->id ?? 3,
                'categorie' => 'Poésie',
                'resume' => 'La beauté sauvage de la savane célébrée en vers.',
                'badge' => null,
                'couleur_couverture' => '#2d4a1a',
                'prix' => 9.99,
                'is_bestseller' => false,
            ],
            [
                'titre' => 'Coeurs Nomades',
                'author_id' => $jbk?->id ?? 2,
                'categorie' => 'Roman',
                'resume' => 'Le voyage intérieur d\'une famille nomade à travers le Sahara.',
                'badge' => 'Populaire',
                'couleur_couverture' => '#4a2d1a',
                'prix' => 11.99,
                'is_bestseller' => false,
            ],
            [
                'titre' => 'Étoiles d\'Abidjan',
                'author_id' => $jbk?->id ?? 2,
                'categorie' => 'Contemporain',
                'resume' => 'La vie nocturne et les rêves de jeunes Abidjanais.',
                'badge' => 'Nouveau',
                'couleur_couverture' => '#1a1a4a',
                'prix' => 12.49,
                'is_bestseller' => false,
            ],
            [
                'titre' => 'La Tisserande',
                'author_id' => $aminata?->id ?? 1,
                'categorie' => 'Historique',
                'resume' => 'Portrait d\'une femme tisserande qui préserve l\'art ancestral de son peuple.',
                'badge' => null,
                'couleur_couverture' => '#4a1a3a',
                'prix' => 13.49,
                'is_bestseller' => false,
            ],
            [
                'titre' => 'Voyage vers Tombouctou',
                'author_id' => $jbk?->id ?? 2,
                'categorie' => 'Aventure',
                'resume' => 'Une quête épique vers la cité mystérieuse du désert.',
                'badge' => 'Classique',
                'couleur_couverture' => '#3a2a1a',
                'prix' => 14.99,
                'is_bestseller' => false,
            ],
            [
                'titre' => 'Chants de la Kora',
                'author_id' => $fatima?->id ?? 3,
                'categorie' => 'Musique & Culture',
                'resume' => 'L\'histoire d\'un griot et la transmission de la mémoire collective.',
                'badge' => null,
                'couleur_couverture' => '#1a3a4a',
                'prix' => 10.49,
                'is_bestseller' => false,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
