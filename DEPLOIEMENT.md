# Déploiement sur Render

Le projet utilise deux services gratuits : le site Laravel et l'agent IA. La base SQLite est recréée avec les données de démonstration à chaque démarrage. Les données saisies ensuite ne sont donc pas conservées.

## Préparer Render

1. Envoyez le projet sur GitHub, sur la branche `feature/agent-ia`.
2. Dans Render, choisissez **New +**, puis **Blueprint** et reliez le dépôt GitHub.
3. Sélectionnez la branche `feature/agent-ia` et validez la configuration de `render.yaml`.
4. Renseignez les variables ci-dessous dans les services Laravel et agent.

## Service Laravel

| Variable | Valeur |
| --- | --- |
| `APP_KEY` | Clé générée avec `php artisan key:generate --show` dans le projet |
| `APP_URL` | URL publique Laravel, par exemple `https://agaueditions-laravel.onrender.com` |
| `AGENT_URL` | URL publique de l'agent, par exemple `https://agaueditions-agent.onrender.com` |
| `ADMIN_PASSWORD` | Mot de passe administrateur à définir, ou laisser vide pour en générer un et l'afficher dans les journaux de démarrage |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `SESSION_DRIVER` | `file` |

## Service agent IA

| Variable | Valeur |
| --- | --- |
| `API_KEY` | Votre clé API Groq, à garder secrète |
| `API_BASE_URL` | `https://api.groq.com/openai/v1` |
| `MODEL` | openai/gpt-oss-120b|
| `LARAVEL_URL` | La même URL publique que `APP_URL`, sans chemin après le nom de domaine |

Les noms de domaine ci-dessus sont des exemples. Après le premier déploiement, copiez les URL réellement affichées par Render dans les variables correspondantes, puis relancez le déploiement.

## À savoir

Le service gratuit Render peut se mettre en veille après une période sans visite. Le premier chargement peut alors prendre un peu de temps. La base SQLite se trouve dans le conteneur et est réinitialisée au démarrage ; ce déploiement convient à une démonstration, pas à la conservation de données réelles. Si `ADMIN_PASSWORD` est vide, le mot de passe généré s'affiche une seule fois dans les journaux de démarrage Laravel. Conservez-le avant le prochain redémarrage.