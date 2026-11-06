# Rapport technique — AGAU Éditions

## État actuel

Le projet utilise Laravel 12.70.1 et SQLite en local. L'analyse initiale a confirmé le schéma des tables `authors` et `books` à partir des migrations et de la base locale.

Les données de démonstration, leurs couvertures et le système de notation sont terminés et vérifiés en local. L'interface de l'agent IA est intégrée, mais son usage réel attend une clé API OpenAI qui n'a pas été ajoutée au projet.

## Données de démonstration

Le seeder ajoute les auteurs suivants s'ils n'existent pas déjà :

- Aminata Diallo
- Jean-Baptiste Konan
- Fatima Ouédraogo

Il ajoute huit livres fictifs, chacun associé à l'un de ces auteurs :

- Le Passage des manguiers
- Les Rivages du silence
- L'Horloge de Grand-Bassam
- La Mémoire des baobabs
- Un hiver à Ouagadougou
- Les Pirogues de l'aube
- Le Marché des étoiles
- Les Jardins de la lagune

`BookTestSeeder` utilise `firstOrCreate` avec le nom de l'auteur et le titre du livre comme critères de recherche. Une nouvelle exécution ne recrée donc pas ces mêmes enregistrements. Chaque livre référence son fichier SVG local sous `images/covers-test/`.

Les seeders préexistants `AuthorSeeder` et `BookSeeder` n'ont pas été modifiés. Ils utilisent `create()` et peuvent ajouter des doublons lorsqu'ils sont relancés. La commande par défaut `php artisan db:seed` les exécute; pour le moment, utiliser la commande ciblée ci-dessous évite de les relancer.

## Catalogue et couvertures

Le catalogue et les fiches détaillées existaient déjà. `FrontController` charge les livres avec leurs auteurs; les vues publiques affichent les titres, auteurs, prix, résumés, couvertures et liens de détail.

Les chemins sont enregistrés dans `books.cover_path`. Le modèle `Book` renvoie maintenant les chemins `images/covers-test/` directement depuis `public/`; les autres chemins continuent d'utiliser le préfixe `storage/` comme auparavant. Les huit fichiers SVG sont servis en HTTP 200. Le catalogue et la fiche détaillée ont été ouverts dans le navigateur et affichent les titres, auteurs et couvertures.

## Système de notation

Une note de 1 à 5 est enregistrée dans `book_ratings`. Un lecteur connecté peut créer, modifier ou supprimer sa propre note depuis la fiche d'un livre. Les visiteurs voient la moyenne et le nombre de notes, et sont invités à se connecter pour noter.

La migration définit les références vers `books` et `users`, supprimées en cascade avec leur parent, et un index unique `(book_id, user_id)` empêchant plusieurs notes d'un même lecteur pour un livre. Laravel valide la note comme un entier entre 1 et 5. La modification et la suppression recherchent la note par l'utilisateur authentifié et le livre; un lecteur ne peut pas modifier la note d'un autre. Les routes exigent `auth` et le middleware existant `not-suspended`.

La moyenne et le nombre affichés sont calculés depuis les lignes enregistrées. Aucun score n'est stocké ou calculé artificiellement.

## Agent IA public

L'agent apparaît seulement sur l'accueil, le catalogue et les fiches de livres, l'index et les fiches de short stories, la connexion et l'inscription. Il reçoit uniquement une question et un contexte limité aux champs publics des 100 premiers livres du catalogue (titre, auteur, catégorie, prix, disponibilité et gratuité). Il n'interroge aucune donnée de compte ou de lecteur; les conversations ne sont pas enregistrées par l'application.

Le fournisseur intégré est l'API OpenAI, modèle `gpt-5.4-mini`, via Laravel HTTP Client et `POST /v1/chat/completions`. La requête utilise `store: false`, limite la réponse à 300 tokens et est limitée à 10 appels par minute côté application. Le navigateur n'obtient jamais la clé. Le site affiche un avertissement demandant de ne pas envoyer d'informations personnelles.

**Comparaison vérifiée le 3 novembre 2026** (prix USD par million de tokens, hors taxes et autres frais) :

| Fournisseur / modèle | Entrée | Sortie | Remarques |
|---|---:|---:|---|
| OpenAI GPT-5.4 Mini (intégré) | $0.75 | $4.50 | Chat Completions pris en charge; API non utilisée pour l'entraînement par défaut. Les journaux de contrôle d'abus peuvent conserver le contenu jusqu'à 30 jours; Zero Data Retention nécessite une approbation. |
| Google Gemini 3.8 Flash | $0.75 | $3.75 jusqu'au 31 décembre 2026 | Service payant; le contenu des requêtes payantes n'est pas utilisé pour améliorer les produits selon la page de tarification. Le niveau gratuit autorise cet usage et n'est pas adapté aux informations personnelles. |
| Anthropic Claude Haiku 5.5 | À partir de $0.10 | À partir de $0.50 | Tarifs indiqués pour les prompts jusqu'à 100 000 tokens; autre fournisseur et intégration distincte. |

Sources officielles : [OpenAI — GPT-5.4 Mini et tarifs](https://developers.openai.com/api/docs/models/gpt-5.4-mini), [OpenAI — conservation des données API](https://developers.openai.com/api/docs/guides/your-data), [Google — tarifs Gemini](https://ai.google.dev/gemini-api/docs/pricing), [Google — conditions Gemini API](https://ai.google.dev/gemini-api/docs/terms), [Anthropic — tarifs](https://platform.claude.com/docs/en/about-claude/pricing).

La clé OpenAI n'est pas configurée dans le `.env` local. Les tests simulent l'API; le fonctionnement en direct ne peut donc pas encore être certifié. La politique de conservation par défaut d'OpenAI signifie que l'application ne stocke pas l'historique, mais ne peut pas garantir l'absence de rétention temporaire côté fournisseur.

Non commencés. Aucune migration, dépendance, route ou fonctionnalité de notation ou d'IA n'a été créée.

## Fichiers créés

- `database/seeders/BookTestSeeder.php` — crée les trois auteurs si nécessaire et les huit livres de test sans réécrire les enregistrements existants.
- `docs/RAPPORT_MODIFICATIONS.md` — présent rapport, à mettre à jour à chaque étape validée.
- `public/images/covers-test/passage-des-manguiers.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/rivages-du-silence.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/horloge-de-grand-bassam.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/memoire-des-baobabs.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/hiver-a-ouagadougou.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/pirogues-de-laube.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/marche-des-etoiles.svg` — couverture fictive du livre correspondant.
- `public/images/covers-test/jardins-de-la-lagune.svg` — couverture fictive du livre correspondant.
- `app/Models/BookRating.php` — modèle des notes et relations vers un livre et un lecteur.
- `app/Http/Controllers/BookRatingController.php` — création, modification et suppression des notes propres à l'utilisateur connecté.
- `database/migrations/2026_11_03_154744_create_book_ratings_table.php` — table des notes, clés étrangères et unicité lecteur/livre.
- `tests/Feature/BookRatingTest.php` — tests de création, validation, agrégation et autorisations des notes.
- `app/Services/PublicCatalogueAssistant.php` — contexte public et appel serveur à l'API OpenAI.
- `app/Http/Controllers/PublicCatalogueAssistantController.php` — validation des questions et gestion explicite des erreurs du fournisseur.
- `resources/views/components/catalogue-assistant.blade.php` — widget de chat public, accessibilité et notice de confidentialité.
- `tests/Feature/PublicCatalogueAssistantTest.php` — tests d'affichage, de données transmises et d'erreurs de l'agent IA.

## Fichiers modifiés

- `database/seeders/DatabaseSeeder.php` — ajoute `BookTestSeeder` après les seeders d'auteurs et de livres existants, sans supprimer ni modifier leurs appels.
- `app/Models/Book.php` — résout les URLs des couvertures locales de test sans changer le traitement des chemins de stockage existants.
- `app/Models/User.php` — ajoute la relation des notes appartenant à un utilisateur.
- `app/Http/Controllers/FrontController.php` — charge la moyenne, le nombre de notes et la note du lecteur sur la fiche livre.
- `routes/web.php` — routes protégées des notes et endpoint public limité de l'assistant.
- `resources/views/front/books/show.blade.php` — affiche les étoiles, moyenne, nombre et formulaire de notation sur les fiches de livres.
- `resources/views/layouts/app.blade.php` et `resources/views/layouts/auth.blade.php` — limitent l'affichage du widget aux pages demandées.
- `config/services.php` et `.env.example` — configurent les paramètres OpenAI sans clé réelle.
- `tests/Feature/FrontControllerTest.php` — vérifie les URL et le rendu des couvertures locales.

## Migrations et dépendances

- Migration créée : `2026_11_03_154744_create_book_ratings_table.php`.
- Table `book_ratings` : `id`, `book_id` (FK `books`, cascade), `user_id` (FK `users`, cascade), `rating` (entier non signé, validé par l'application entre 1 et 5), `created_at`, `updated_at`.
- Contrainte unique : `(book_id, user_id)`.
- Migration exécutée uniquement sur la base SQLite locale; aucune migration de production n'a été exécutée.
- Dépendances Composer ou npm ajoutées ou modifiées : aucune.
- Nouvelle table : `book_ratings`; aucune colonne ajoutée aux tables existantes.

## Configuration et données sensibles

La connexion locale est configurée sur SQLite. `DB_DATABASE` n'est pas défini dans `.env`; Laravel utilise donc `database/database.sqlite`. `.env` est ignoré par Git et n'a pas été modifié.

Variables ajoutées à `.env.example` :

```dotenv
OPENAI_API_KEY=
OPENAI_MODEL=gpt-5.4-mini
```

Pour activer l'agent, créer une clé API depuis [OpenAI Platform](https://platform.openai.com/api-keys), puis ajouter localement `OPENAI_API_KEY=...` dans `.env` et laisser `OPENAI_MODEL=gpt-5.4-mini` (ou définir un modèle disponible). Ne pas copier la vraie clé dans Git, le navigateur, les journaux ou `.env.example`. Exécuter `php artisan config:clear` après modification et redémarrer le serveur Laravel. En l'absence de clé, l'endpoint retourne explicitement HTTP 503; aucun appel n'est envoyé.

## Commandes et validation

Seeder ciblé :

```powershell
php artisan db:seed --class=BookTestSeeder --no-interaction
```

Cette commande a été exécutée deux fois. Une vérification en lecture seule de SQLite a confirmé :

- 3 auteurs de démonstration;
- 8 livres de démonstration;
- 8 associations livre-auteur valides;
- aucun doublon parmi les chemins de couverture enregistrés.

Le formatage PHP a été vérifié avec `vendor/bin/pint --dirty --format agent`. `php artisan test --compact tests/Feature/FrontControllerTest.php` a réussi : 7 tests, 19 assertions. Les huit fichiers SVG ont été contrôlés par analyse XML et servis en HTTP 200. Les pages catalogue et détail ont été vérifiées dans le navigateur.

Notation : `php artisan test --compact tests/Feature/BookRatingTest.php` — 5 tests, 31 assertions réussis.

Agent IA : `php artisan test --compact tests/Feature/PublicCatalogueAssistantTest.php` — 5 tests, 30 assertions réussis. L'appel fournisseur a été simulé avec le client HTTP Laravel; aucune clé réelle ni requête payante n'a été utilisée.

Au total, les trois fichiers de tests ciblés (`BookRatingTest`, `PublicCatalogueAssistantTest`, `FrontControllerTest`) ont réussi : 17 tests et 80 assertions. `git diff --check` ne signale pas d'erreur.

La migration a été exécutée localement avec `php artisan migrate --no-interaction`. La base contient toujours les 8 livres et 3 auteurs de démonstration. Une sauvegarde antérieure à cette migration est conservée hors du dépôt sous `C:\Users\SAM\.copilot\session-state\de0df1cd-7d15-4e0b-b9f4-54682edf78d3\files\database-before-rating-migration.sqlite`.

## Difficultés et limites restantes

- Le lien `public/storage` n'existe pas dans l'environnement local; le choix retenu pour les couvertures de test est néanmoins `public/images/covers-test/`.
- L'exécution répétée des seeders historiques via `DatabaseSeeder` peut toujours dupliquer leurs données.
- Les données de démonstration locales ne sont pas des données de production.
- L'agent ne peut pas appeler OpenAI en direct avant l'ajout d'une clé API valide au `.env`; le test réel, les quotas et la facturation restent à vérifier. Les tests automatisés couvrent l'affichage du widget aux seules pages autorisées.
- OpenAI peut conserver les contenus d'API dans des journaux de contrôle d'abus jusqu'à 30 jours par défaut. Il faut éviter toute donnée personnelle; une politique Zero Data Retention requiert l'éligibilité et l'approbation du fournisseur.

## Intégration et déploiement

Ne pas déployer les données de démonstration en production sans décision explicite. Ajouter les SVG sous `public/images/covers-test/` si ces données doivent être conservées; exécuter le seeder ciblé ci-dessus pour les recréer en local.

Sur l'environnement cible, sauvegarder d'abord la base, puis exécuter `php artisan migrate --force` pour créer `book_ratings`. Ne jamais utiliser `migrate:fresh` ni `db:wipe`. Déployer les nouveaux fichiers et les variables de configuration; aucune dépendance Composer ou npm supplémentaire n'est requise. Configurer la clé OpenAI via le gestionnaire de secrets de l'environnement, vider le cache de configuration et redémarrer les workers/serveurs. Vérifier les routes et exécuter les tests ciblés avant ouverture au public. La clé doit rester côté serveur.
