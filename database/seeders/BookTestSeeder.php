<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;

class BookTestSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            'AD' => Author::firstOrCreate(
                ['nom' => 'Aminata Diallo'],
                [
                    'initiales' => 'AD',
                    'bio' => 'Romancière sénégalaise, elle explore les liens entre mémoire familiale et vie contemporaine.',
                    'couleur_avatar' => '#1B6CA8',
                    'nb_livres' => 0,
                ],
            ),
            'JBK' => Author::firstOrCreate(
                ['nom' => 'Jean-Baptiste Konan'],
                [
                    'initiales' => 'JBK',
                    'bio' => 'Écrivain ivoirien, il raconte les transformations des villes et des sociétés ouest-africaines.',
                    'couleur_avatar' => '#0A3050',
                    'nb_livres' => 0,
                ],
            ),
            'FO' => Author::firstOrCreate(
                ['nom' => 'Fatima Ouédraogo'],
                [
                    'initiales' => 'FO',
                    'bio' => 'Autrice burkinabè, ses récits célèbrent la transmission et la force des communautés.',
                    'couleur_avatar' => '#2A8ACF',
                    'nb_livres' => 0,
                ],
            ),
        ];

        $books = [
            [
                'titre' => 'Le Passage des manguiers',
                'author' => 'AD',
                'categorie' => 'Roman',
                'resume' => 'À la veille d’un départ, une jeune femme redécouvre les histoires que sa famille a gardées sous silence.',
                'badge' => 'Nouveau',
                'couleur_couverture' => '#B85C38',
                'prix' => 4500,
                'is_bestseller' => false,
                'cover' => 'passage-des-manguiers.svg',
            ],
            [
                'titre' => 'Les Rivages du silence',
                'author' => 'AD',
                'categorie' => 'Littérature contemporaine',
                'resume' => 'Une correspondance inattendue rapproche deux générations séparées par l’océan.',
                'badge' => null,
                'couleur_couverture' => '#2C6E75',
                'prix' => 5000,
                'is_bestseller' => false,
                'cover' => 'rivages-du-silence.svg',
            ],
            [
                'titre' => "L'Horloge de Grand-Bassam",
                'author' => 'JBK',
                'categorie' => 'Roman historique',
                'resume' => 'La restauration d’une vieille horloge révèle les destins croisés d’une ville côtière.',
                'badge' => 'Coup de cœur',
                'couleur_couverture' => '#8C5E35',
                'prix' => 5500,
                'is_bestseller' => true,
                'cover' => 'horloge-de-grand-bassam.svg',
            ],
            [
                'titre' => 'La Mémoire des baobabs',
                'author' => 'FO',
                'categorie' => 'Poésie',
                'resume' => 'Des poèmes traversent les saisons, les souvenirs et les voix d’un village.',
                'badge' => null,
                'couleur_couverture' => '#4D6B3C',
                'prix' => 4000,
                'is_bestseller' => false,
                'cover' => 'memoire-des-baobabs.svg',
            ],
            [
                'titre' => 'Un hiver à Ouagadougou',
                'author' => 'FO',
                'categorie' => 'Roman',
                'resume' => 'Pendant une saison de changements, trois amis cherchent leur place dans une ville en mouvement.',
                'badge' => 'Nouveau',
                'couleur_couverture' => '#69518C',
                'prix' => 5000,
                'is_bestseller' => false,
                'cover' => 'hiver-a-ouagadougou.svg',
            ],
            [
                'titre' => "Les Pirogues de l'aube",
                'author' => 'JBK',
                'categorie' => 'Aventure',
                'resume' => 'Un voyage au fil de l’eau conduit un jeune pêcheur vers une promesse oubliée.',
                'badge' => null,
                'couleur_couverture' => '#277DA1',
                'prix' => 6000,
                'is_bestseller' => false,
                'cover' => 'pirogues-de-laube.svg',
            ],
            [
                'titre' => 'Le Marché des étoiles',
                'author' => 'AD',
                'categorie' => 'Littérature contemporaine',
                'resume' => 'Au cœur d’un marché animé, les rêves de commerçants et de passants se répondent.',
                'badge' => null,
                'couleur_couverture' => '#8C4A67',
                'prix' => 4500,
                'is_bestseller' => false,
                'cover' => 'marche-des-etoiles.svg',
            ],
            [
                'titre' => 'Les Jardins de la lagune',
                'author' => 'JBK',
                'categorie' => 'Roman',
                'resume' => 'Une famille tente de préserver son jardin et ses traditions face aux changements de la ville.',
                'badge' => 'À découvrir',
                'couleur_couverture' => '#477A5B',
                'prix' => 5000,
                'is_bestseller' => false,
                'cover' => 'jardins-de-la-lagune.svg',
            ],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                ['titre' => $book['titre']],
                [
                    'author_id' => $authors[$book['author']]->id,
                    'categorie' => $book['categorie'],
                    'resume' => $book['resume'],
                    'badge' => $book['badge'],
                    'format' => 'Numérique (PDF)',
                    'disponibilite' => 'Immédiate',
                    'maison_edition' => 'AGAU Éditions',
                    'couleur_couverture' => $book['couleur_couverture'],
                    'cover_path' => 'images/covers-test/'.$book['cover'],
                    'prix' => $book['prix'],
                    'is_bestseller' => $book['is_bestseller'],
                    'is_free' => false,
                ],
            );
        }
    }
}
