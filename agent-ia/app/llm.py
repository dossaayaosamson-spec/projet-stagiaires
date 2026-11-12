"""Communication avec l'API compatible OpenAI de Groq."""

import json
import logging

from openai import (
    APIConnectionError,
    APIStatusError,
    APITimeoutError,
    AuthenticationError,
    OpenAI,
    OpenAIError,
)

from app.config import API_BASE_URL, API_KEY, MODEL, validate_api_settings
from app.prompts import SYSTEM_PROMPT


class GroqAuthenticationError(Exception):
    """La clé API Groq a été refusée."""


class GroqConnectionError(Exception):
    """Le service n'a pas pu joindre Groq."""


class GroqTimeoutError(Exception):
    """La requête vers Groq a dépassé le délai autorisé."""


class GroqProviderError(Exception):
    """Groq a renvoyé une erreur ou une réponse inutilisable."""


def _log_groq_error(error: OpenAIError) -> None:
    """Journalise les détails utiles sans risquer d'afficher la clé API."""
    status_code = error.status_code if isinstance(error, APIStatusError) else None
    message = str(error)

    if isinstance(error, APIStatusError):
        body = error.body
        if isinstance(body, dict):
            provider_error = body.get("error")
            if isinstance(provider_error, dict):
                message = str(provider_error.get("message") or message)
            elif isinstance(provider_error, str):
                message = provider_error
        elif body is not None:
            message = str(body)

    if API_KEY:
        message = message.replace(API_KEY, "[REDACTED]")

    logging.error(
        "Échec de l'appel Groq : type=%s, code_http=%s, message=%s",
        type(error).__name__,
        status_code if status_code is not None else "N/A",
        message,
    )


def sanitize_response(response: str) -> str:
    """Supprime les marques Markdown et les lignes de tableaux."""
    readable_lines = []

    for line in response.splitlines():
        stripped_line = line.strip()
        if "|" in stripped_line and stripped_line.count("|") >= 2:
            continue
        if stripped_line and all(character in "|:- " for character in stripped_line):
            continue

        readable_lines.append(
            line.replace("**", "")
            .replace("__", "")
            .replace("#", "")
            .replace("|", " ")
        )

    return "\n".join(readable_lines).strip()


def ask_groq(
    message: str,
    connaissance: list[dict[str, str]] | None = None,
    historique: list[dict[str, str]] | None = None,
) -> str:
    """Envoie une question à Groq et retourne uniquement sa réponse."""
    validate_api_settings()
    system_prompt = SYSTEM_PROMPT

    if connaissance:
        knowledge_json = json.dumps(connaissance, ensure_ascii=False)
        system_prompt = f"{SYSTEM_PROMPT}\n\nConnaissance publique disponible (JSON) :\n{knowledge_json}"

    try:
        with OpenAI(
            api_key=API_KEY,
            base_url=API_BASE_URL,
            timeout=30.0,
            max_retries=0,
        ) as client:
            messages = [
                {"role": "system", "content": system_prompt},
                *(historique or []),
                {"role": "user", "content": message},
            ]
            completion = client.chat.completions.create(
                model=MODEL,
                messages=messages,
            )
    except AuthenticationError as error:
        _log_groq_error(error)
        raise GroqAuthenticationError(
            "La clé API Groq a été refusée. Vérifiez API_KEY dans agent-ia/.env."
        ) from error
    except APITimeoutError as error:
        _log_groq_error(error)
        raise GroqTimeoutError(
            "Groq n'a pas répondu dans le délai prévu."
        ) from error
    except APIConnectionError as error:
        _log_groq_error(error)
        raise GroqConnectionError(
            "Impossible de joindre Groq. Vérifiez votre connexion Internet."
        ) from error
    except APIStatusError as error:
        _log_groq_error(error)
        raise GroqProviderError(
            "Le fournisseur d'intelligence artificielle a renvoyé une erreur."
        ) from error
    except OpenAIError as error:
        _log_groq_error(error)
        raise GroqProviderError(
            "La communication avec le fournisseur d'intelligence artificielle a échoué."
        ) from error

    if not completion.choices:
        raise GroqProviderError("Le fournisseur n'a pas renvoyé de réponse.")

    content = completion.choices[0].message.content
    if not isinstance(content, str) or not content.strip():
        raise GroqProviderError("Le fournisseur a renvoyé une réponse vide.")

    cleaned_content = sanitize_response(content)
    if not cleaned_content:
        raise GroqProviderError("La réponse du fournisseur est vide après nettoyage.")

    return cleaned_content
