"""Chargement de la configuration locale du service."""

from pathlib import Path
import os

from dotenv import dotenv_values

ENV_FILE = Path(__file__).resolve().parents[1] / ".env"
_ENV = dotenv_values(ENV_FILE)

API_KEY = (os.environ.get("API_KEY") or _ENV.get("API_KEY") or "").strip()
API_BASE_URL = (
    os.environ.get("API_BASE_URL")
    or _ENV.get("API_BASE_URL")
    or "https://api.groq.com/openai/v1"
).strip()
MODEL = (
    os.environ.get("MODEL") or _ENV.get("MODEL") or "llama-3.3-70b-versatile"
).strip()
LARAVEL_URL = (
    os.environ.get("LARAVEL_URL") or _ENV.get("LARAVEL_URL") or "http://localhost:8000"
).strip().rstrip("/")


def validate_api_settings() -> None:
    """Vérifie les paramètres nécessaires avant un appel au fournisseur."""
    if not API_KEY:
        raise ValueError(
            "La clé API Groq est absente. Renseignez API_KEY dans agent-ia/.env."
        )

    if not API_BASE_URL:
        raise ValueError("L'adresse API est absente de agent-ia/.env.")

    if not MODEL:
        raise ValueError("Le nom du modèle est absent de agent-ia/.env.")
