<style>
    .catalogue-assistant-launcher {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 120;
        border: 0;
        border-radius: 999px;
        padding: 14px 20px;
        background: #1E3A8A;
        color: #F3EADB;
        font-weight: 700;
        box-shadow: 0 8px 24px rgba(30, 58, 138, .2);
        cursor: pointer;
    }

    .catalogue-assistant-panel {
        position: fixed;
        right: 24px;
        bottom: 86px;
        z-index: 120;
        display: flex;
        width: min(380px, calc(100vw - 32px));
        height: min(520px, calc(100vh - 120px));
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #E3D5BC;
        border-radius: 16px;
        background: #FBF7F0;
        box-shadow: 0 12px 40px rgba(30, 41, 59, .18);
    }

    .catalogue-assistant-panel[hidden] {
        display: none;
    }

    .catalogue-assistant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 2px solid #F3EADB;
        background: #0F4A7A;
        color: #F3EADB;
    }

    .catalogue-assistant-title {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        line-height: 1.3;
    }

    .catalogue-assistant-close {
        flex: 0 0 auto;
        border: 0;
        background: transparent;
        color: #F3EADB;
        font-size: 1.75rem;
        line-height: 1;
        cursor: pointer;
    }

    .catalogue-assistant-messages {
        display: flex;
        flex: 1;
        min-height: 0;
        flex-direction: column;
        gap: 10px;
        overflow-y: auto;
        margin: 0;
        padding: 16px;
        background: #FBF7F0;
        list-style: none;
    }

    .catalogue-assistant-message {
        max-width: 90%;
        border: 1px solid #E3D5BC;
        border-radius: 12px;
        padding: 10px 12px;
        background: #F1E8D8;
        color: #3B2F2A;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .catalogue-assistant-message.is-user {
        align-self: flex-end;
        border-color: transparent;
        background: #1D4ED8;
        color: #fff;
    }

    .catalogue-assistant-form {
        display: flex;
        flex: 0 0 auto;
        gap: 8px;
        padding: 12px;
        border-top: 1px solid #e5eaf0;
    }

    .catalogue-assistant-form input {
        min-width: 0;
        flex: 1;
        border: 1px solid #E3D5BC;
        border-radius: 10px;
        padding: 10px;
    }

    .catalogue-assistant-form input:focus {
        border-color: #1E3A8A;
        outline: 2px solid rgba(30, 58, 138, .15);
        outline-offset: 1px;
    }

    .catalogue-assistant-form button {
        border: 0;
        border-radius: 10px;
        padding: 10px 14px;
        background: #1E3A8A;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .catalogue-assistant-form button:disabled {
        opacity: .6;
        cursor: wait;
    }

    .catalogue-assistant-privacy {
        flex: 0 0 auto;
        margin: 0;
        padding: 0 10px 6px;
        color: #64748b;
        font-size: .65rem;
        line-height: 1.2;
    }

    @media (max-width: 480px) {
        .catalogue-assistant-launcher {
            right: 16px;
            bottom: 16px;
        }

        .catalogue-assistant-panel {
            right: 12px;
            bottom: 76px;
            width: calc(100vw - 24px);
            height: min(520px, calc(100dvh - 96px));
        }
    }
</style>

<button
    type="button"
    class="catalogue-assistant-launcher"
    id="catalogue-assistant-launcher"
    aria-controls="catalogue-assistant-panel"
    aria-expanded="false"
>
    Une question sur nos livres ?
</button>

<section class="catalogue-assistant-panel" id="catalogue-assistant-panel" aria-label="Assistant AGAU ÉDITIONS" hidden>
    <div class="catalogue-assistant-header">
        <strong class="catalogue-assistant-title">Assistant AGAU ÉDITIONS</strong>
        <button type="button" class="catalogue-assistant-close" id="catalogue-assistant-close" aria-label="Fermer">&times;</button>
    </div>
    <ol class="catalogue-assistant-messages" id="catalogue-assistant-messages" aria-live="polite">
        <li class="catalogue-assistant-message">Bonjour ! Je peux vous renseigner sur les livres, leurs auteurs, leurs prix et leur disponibilité.</li>
    </ol>
    <form class="catalogue-assistant-form" id="catalogue-assistant-form">
        <label for="catalogue-assistant-input" class="sr-only">Votre question</label>
        <input id="catalogue-assistant-input" name="message" maxlength="500" required autocomplete="off" placeholder="Écrivez votre question…">
        <button type="submit">Envoyer</button>
    </form>
    <p class="catalogue-assistant-privacy">Vos messages sont transmis à un service d'intelligence artificielle pour générer la réponse. Évitez d'inclure des informations personnelles.</p>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const launcher = document.getElementById('catalogue-assistant-launcher');
        const panel = document.getElementById('catalogue-assistant-panel');
        const closeButton = document.getElementById('catalogue-assistant-close');
        const form = document.getElementById('catalogue-assistant-form');
        const input = document.getElementById('catalogue-assistant-input');
        const messages = document.getElementById('catalogue-assistant-messages');
        const conversationHistory = [];

        if (!launcher || !panel || !closeButton || !form || !input || !messages) {
            return;
        }

        function setOpen(isOpen) {
            panel.hidden = !isOpen;
            launcher.setAttribute('aria-expanded', String(isOpen));
            if (isOpen) {
                input.focus();
            } else {
                launcher.focus();
            }
        }

        function addMessage(text, isUser) {
            const item = document.createElement('li');
            item.className = 'catalogue-assistant-message' + (isUser ? ' is-user' : '');
            item.textContent = text;
            messages.appendChild(item);
            messages.scrollTop = messages.scrollHeight;
        }

        launcher.addEventListener('click', function () {
            setOpen(panel.hidden);
        });
        closeButton.addEventListener('click', function () {
            setOpen(false);
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const message = input.value.trim();

            if (!message) {
                return;
            }

            addMessage(message, true);
            input.value = '';
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            const previousHistory = conversationHistory.slice(-6).map(function (entry) {
                return {
                    role: entry.role,
                    content: entry.content.slice(-500)
                };
            });
            const typingIndicator = document.createElement('li');
            typingIndicator.className = 'catalogue-assistant-message';
            typingIndicator.textContent = "L'assistant écrit...";
            messages.appendChild(typingIndicator);
            messages.scrollTop = messages.scrollHeight;

            try {
                const response = await fetch(@json(route('assistant.chat', [], false)), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: message,
                        historique: previousHistory
                    })
                });

                if (!response.ok) {
                    throw new Error('La demande n’a pas pu être traitée.');
                }

                const result = await response.json();
                if (typeof result.reponse !== 'string' || !result.reponse.trim()) {
                    throw new Error('La réponse de l’assistant est indisponible.');
                }

                addMessage(result.reponse, false);
                conversationHistory.push(
                    { role: 'user', content: message },
                    { role: 'assistant', content: result.reponse }
                );
                conversationHistory.splice(0, Math.max(0, conversationHistory.length - 6));
            } catch (error) {
                addMessage('L’assistant est momentanément indisponible, réessayez plus tard.', false);
            } finally {
                typingIndicator.remove();
                submitButton.disabled = false;
                input.focus();
            }
        });
    });
</script>
