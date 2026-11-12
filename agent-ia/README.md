# Service Python de l'agent IA

Ce service FastAPI permet à l'assistant de AGAU Éditions d'envoyer une question
et un contexte public fourni par Laravel à Groq. Le service Python ne se
connecte pas directement à SQLite.

## Fichiers principaux

- `app/main.py` : application FastAPI et routes `/health` et `/chat`.
- `app/config.py` : lecture et vérification de la configuration.
- `app/llm.py` : communication avec l'API Groq.
- `app/prompts.py` : consignes en français données à l'assistant.
- `app/__init__.py` : marque `app` comme package Python.
- `.env` : paramètres privés locaux, à ne pas envoyer sur GitHub.
- `.env.example` : exemple des paramètres à renseigner.

## Installation

Depuis la racine du projet, créez l'environnement virtuel :

```powershell
python -m venv agent-ia\venv
```

Activez-le dans PowerShell :

```powershell
.\agent-ia\venv\Scripts\Activate.ps1
```

Si PowerShell bloque l'activation, vous pouvez appeler directement les
exécutables de l'environnement sans l'activer.

Installez les dépendances :

```powershell
python -m pip install -r agent-ia\requirements.txt
```

Copiez les paramètres de `.env.example` dans `agent-ia\.env`, puis renseignez
votre clé dans la ligne `API_KEY=`. Ne partagez pas ce fichier et ne publiez
jamais la clé.

## Lancement

Depuis la racine du projet, lancez le service :

```powershell
.\agent-ia\venv\Scripts\python.exe -m uvicorn app.main:app --reload --host 127.0.0.1 --port 8001 --app-dir agent-ia
```

Le service écoute sur `http://127.0.0.1:8001`.

## Vérification et test

- État du service : ouvrez `http://127.0.0.1:8001/health`. La réponse attendue
  contient `"status": "ok"`.
- Documentation interactive : ouvrez `http://127.0.0.1:8001/docs`.
- Pour tester `/chat`, ouvrez `/docs`, choisissez `POST /chat`, cliquez sur
  « Try it out », puis envoyez par exemple :

```json
{
  "message": "Bonjour, pouvez-vous vous présenter ?"
}
```

La requête accepte aussi un champ `connaissance` optionnel contenant une liste
d'informations publiques en JSON. Le contexte est limité à 6 000 caractères;
la route fonctionne également sans ce champ. Une clé Groq valide est nécessaire
pour obtenir une réponse du modèle. Sans clé, le service reste démarré et
`/chat` renvoie une erreur de configuration explicite.

## Arrêt

Dans le terminal qui exécute Uvicorn, appuyez sur `Ctrl+C`.

## Lancer Laravel et Python ensemble

Ouvrez deux terminaux à la racine du projet. Dans le premier, lancez Laravel
avec `php artisan serve` (port 8000 par défaut). Dans le second, lancez la
commande Uvicorn ci-dessus (port 8001). Arrêtez chaque serveur dans son
terminal avec `Ctrl+C`.
