"""Application FastAPI de l'agent IA AGAU Éditions."""

import json
from typing import Literal

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field, field_validator

from app.config import LARAVEL_URL
from app.llm import (
    GroqAuthenticationError,
    GroqConnectionError,
    GroqProviderError,
    GroqTimeoutError,
    ask_groq,
)

app = FastAPI(
    title="AGAU Éditions - Agent IA",
    description="Service de discussion avec l'assistant IA de AGAU Éditions.",
    version="1.0.0",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=[LARAVEL_URL],
    allow_credentials=False,
    allow_methods=["GET", "POST"],
    allow_headers=["Accept", "Content-Type"],
)


class HistoryMessage(BaseModel):
    """Un message précédent de cette conversation."""

    role: Literal["user", "assistant"]
    content: str = Field(min_length=1, max_length=500)


class ChatRequest(BaseModel):
    """Question reçue par le service."""

    message: str = Field(min_length=1, max_length=1000)
    connaissance: list[dict[str, str]] = Field(default_factory=list, max_length=100)
    historique: list[HistoryMessage] = Field(default_factory=list, max_length=6)

    @field_validator("message")
    @classmethod
    def strip_message(cls, message: str) -> str:
        """Supprime les espaces en trop et refuse les messages vides."""
        message = message.strip()
        if not message:
            raise ValueError("Le message ne peut pas être vide.")
        return message

    @field_validator("connaissance")
    @classmethod
    def limit_knowledge_size(cls, connaissance: list[dict[str, str]]) -> list[dict[str, str]]:
        """Limite la taille du contexte transmis au modèle."""
        serialized = json.dumps(connaissance, ensure_ascii=False)
        if len(serialized) > 6000:
            raise ValueError("Le contexte public dépasse la taille maximale autorisée.")
        return connaissance


@app.get("/health")
def health() -> dict[str, str]:
    """Confirme que le service est démarré."""
    return {"status": "ok", "service": "AGAU Éditions - Agent IA"}


@app.post("/chat")
def chat(request: ChatRequest) -> dict[str, str]:
    """Transmet une question au modèle et retourne sa réponse."""
    try:
        answer = ask_groq(
            request.message,
            request.connaissance,
            [message.model_dump() for message in request.historique],
        )
    except ValueError as error:
        raise HTTPException(status_code=503, detail=str(error)) from error
    except GroqAuthenticationError as error:
        raise HTTPException(status_code=502, detail=str(error)) from error
    except GroqTimeoutError as error:
        raise HTTPException(status_code=504, detail=str(error)) from error
    except GroqConnectionError as error:
        raise HTTPException(status_code=503, detail=str(error)) from error
    except GroqProviderError as error:
        raise HTTPException(status_code=502, detail=str(error)) from error

    return {"reponse": answer}
