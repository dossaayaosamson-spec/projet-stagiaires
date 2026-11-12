<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\ShortStory;
use Illuminate\Support\Str;

class AssistantKnowledge
{
    private const MAX_CONTEXT_CHARACTERS = 6000;

    private const STOP_WORDS = [
        'avec', 'dans', 'des', 'est', 'et', 'les', 'livre', 'livres', 'moi',
        'pour', 'quel', 'quelle', 'quels', 'quelles', 'qui', 'sur', 'une',
    ];

    /**
     * Construit un contexte limité aux informations publiques autorisées.
     *
     * @return array<int, array<string, string>>
     */
    public function forQuestion(string $question): array
    {
        $items = [
            ...$this->siteKnowledge(),
            ...$this->planKnowledge(),
            ...$this->authorKnowledge(),
            ...$this->bookKnowledge(),
            ...$this->storyKnowledge(),
        ];
        $items = array_map(
            fn (array $item): array => array_map(
                fn (string $value): string => $this->withoutEmailAddresses($value),
                $item
            ),
            $items
        );

        $serializedItems = json_encode($items, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (mb_strlen($serializedItems) <= self::MAX_CONTEXT_CHARACTERS) {
            return $items;
        }

        return $this->selectRelevantItems($items, $question);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function authorKnowledge(): array
    {
        return Author::query()
            ->select(['id', 'nom', 'bio'])
            ->latest()
            ->get()
            ->map(fn (Author $author): array => [
                'type' => 'auteur',
                'nom' => (string) $author->nom,
                'biographie' => $this->publicText($author->bio, 500),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function bookKnowledge(): array
    {
        return Book::query()
            ->select(['id', 'titre', 'author_id', 'categorie', 'resume', 'prix', 'disponibilite', 'is_free'])
            ->with('author:id,nom,bio')
            ->latest()
            ->get()
            ->map(fn (Book $book): array => [
                'type' => 'livre',
                'titre' => (string) $book->titre,
                'auteur' => (string) ($book->author?->nom ?? 'Non renseigné'),
                'biographie_auteur' => $this->publicText($book->author?->bio, 300),
                'categorie' => (string) ($book->categorie ?? 'Non renseignée'),
                'prix' => number_format((float) $book->prix, 0, ',', ' ').' FCFA',
                'disponibilite' => (string) ($book->disponibilite ?? 'Non renseignée'),
                'gratuit' => $book->is_free ? 'oui' : 'non',
                'resume' => $this->publicText($book->resume, 200),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function storyKnowledge(): array
    {
        return ShortStory::query()
            ->select(['titre', 'auteur', 'extrait', 'tags'])
            ->latest()
            ->get()
            ->map(fn (ShortStory $story): array => [
                'type' => 'short story',
                'titre' => (string) $story->titre,
                'auteur' => (string) ($story->auteur ?? 'Non renseigné'),
                'tags' => is_array($story->tags) ? implode(', ', $story->tags) : '',
                'resume' => $this->publicText($story->extrait, 200),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function planKnowledge(): array
    {
        return collect(config('plans', []))
            ->map(fn (array $plan): array => [
                'type' => 'offre',
                'nom' => (string) ($plan['nom'] ?? ''),
                'prix' => number_format((float) ($plan['prix'] ?? 0), 0, ',', ' ').' FCFA',
                'description' => $this->publicText($plan['description'] ?? null, 200),
                'avantages' => implode(', ', $plan['avantages'] ?? []),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function siteKnowledge(): array
    {
        return [
            [
                'type' => 'blog',
                'titre' => 'Blog & Discussions',
                'description' => 'Rubrique publique consacrée aux échanges autour de la littérature africaine, de l’édition et de la culture.',
            ],
            [
                'type' => 'contact',
                'adresse' => 'Adidogomé ruelle cité Mokpokpo, Lomé, 23 BP 63',
                'telephone' => '+228 70 76 76 94',
                'horaires' => 'Du lundi au vendredi, de 9h00 à 17h00.',
                'page' => 'Contact',
            ],
        ];
    }

    private function publicText(?string $value, int $limit): string
    {
        $text = trim(strip_tags((string) $value));

        return mb_substr($this->withoutEmailAddresses($text), 0, $limit);
    }

    private function withoutEmailAddresses(string $value): string
    {
        return preg_replace('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', '[adresse retirée]', $value) ?? $value;
    }

    /**
     * @param  array<int, array<string, string>>  $items
     * @return array<int, array<string, string>>
     */
    private function selectRelevantItems(array $items, string $question): array
    {
        $keywords = collect(preg_split('/[^\p{L}\p{N}]+/u', Str::lower($question)) ?: [])
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3 && ! in_array($word, self::STOP_WORDS, true))
            ->unique()
            ->values()
            ->all();

        $rankedItems = collect($items)
            ->map(function (array $item, int $index) use ($keywords): array {
                $text = Str::lower(implode(' ', $item));
                $score = 0;

                foreach ($keywords as $keyword) {
                    if (str_contains($text, $keyword)) {
                        $score++;
                    }
                }

                return ['item' => $item, 'score' => $score, 'index' => $index];
            })
            ->all();

        usort($rankedItems, function (array $left, array $right): int {
            return ($right['score'] <=> $left['score'])
                ?: ($left['index'] <=> $right['index']);
        });

        $hasRelevantItems = collect($rankedItems)->contains(
            fn (array $item): bool => $item['score'] > 0
        );

        $selected = [];
        $characterCount = 2;

        foreach ($rankedItems as $rankedItem) {
            if ($hasRelevantItems && $rankedItem['score'] === 0) {
                continue;
            }

            $item = $rankedItem['item'];
            $itemLength = mb_strlen(json_encode($item, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            $separatorLength = $selected === [] ? 0 : 1;

            if ($characterCount + $itemLength + $separatorLength > self::MAX_CONTEXT_CHARACTERS) {
                continue;
            }

            $selected[] = $item;
            $characterCount += $itemLength + $separatorLength;
        }

        return $selected;
    }
}
