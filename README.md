# AGAU Editions

Plateforme Laravel pour une maison d edition numerique:
- catalogue de livres, auteurs, nouvelles et evenements
- espace client, auteur et administration
- achat et paiement FedaPay
- lecture PDF avec controle d acces

## Stack technique

- PHP 8.4
- Laravel 12
- MySQL (dev/prod)
- SQLite en memoire pour les tests
- Vite + Tailwind CSS

## Installation locale

1. Installer les dependances:

```bash
composer install
npm install
```

2. Configurer l environnement:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configurer la base de donnees dans `.env` puis lancer:

```bash
php artisan migrate --seed
```

4. Lancer le projet:

```bash
composer run dev
```

## Variables d environnement importantes

- `DB_*` pour la base de donnees locale
- `FEDAPAY_API_KEY` pour les paiements
- `FEDAPAY_ENVIRONMENT` (`sandbox` ou `live`)

## Tests

Lancer toute la suite:

```bash
php artisan test
```

La suite de tests est configuree sur SQLite en memoire via `phpunit.xml` et `.env.testing` pour des executions stables et rapides.

## Procedure de reset DB (dev/test)

### Reset complet developpement

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
```

### Verification de la base de test

```bash
php artisan test
```

En test, aucune base MySQL locale n est necessaire.

## Paiement FedaPay

La configuration est centralisee dans `config/services.php`:
- `services.fedapay.api_key`
- `services.fedapay.environment`

La logique API est encapsulee dans `app/Services/Payments/FedaPayPaymentService.php`.
