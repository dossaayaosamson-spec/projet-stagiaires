<style>
    .catalogue-assistant-launcher {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 120;
        border: 0;
        border-radius: 999px;
        padding: 14px 20px;
        background: #1B6CA8;
        color: #fff;
        font-weight: 700;
        box-shadow: 0 8px 24px rgba(15, 74, 122, .25);
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
        border: 1px solid #dce5ed;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 12px 40px rgba(15, 48, 80, .2);
    }

    .catalogue-assistant-panel[hidden] {
        display: none;
    }

    .catalogue-assistant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #0F4A7A;
        color: #fff;
    }

    .catalogue-assistant-close {
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .catalogue-assistant-messages {
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 10px;
        overflow-y: auto;
        padding: 16px;
        list-style: none;
    }

    .catalogue-assistant-message {
        max-width: 90%;
        border-radius: 12px;
        padding: 10px 12px;
        background: #f0f4f8;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .catalogue-assistant-message.is-user {
        align-self: flex-end;
        background: #e7f2fa;
    }

    .catalogue-assistant-form {
        display: flex;
        gap: 8px;
        padding: 12px;
        border-top: 1px solid #e5eaf0;
    }

    .catalogue-assistant-form input {
        min-width: 0;
        flex: 1;
        border: 1px solid #cbd5df;
        border-radius: 8px;
        padding: 10px;
    }

    .catalogue-assistant-form button {
        border: 0;
        border-radius: 8px;
        padding: 10px 14px;
        background: #1B6CA8;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .catalogue-assistant-form button:disabled {
        opacity: .6;
        cursor: wait;
    }

    .catalogue-assistant-privacy {
        padding: 0 12px 10px;
        color: #64748b;
        font-size: .72rem;
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

<section class="catalogue-assistant-panel" id="catalogue-assistant-panel" aria-label="Assistant AGAU Éditions" hidden>
    <div class="catalogue-assistant-header">
        <strong>Assistant AGAU Éditions</strong>
        <button type="button" class="catalogue-assistant-close" id="catalogue-assistant-close" aria-label="Fermer">&times;</button>
    </div>
    <ol class="catalogue-assistant-messages" id="catalogue-assistant-messages" aria-live="polite">
        <li class="catalogue-assistant-message">Bonjour ! Je peux vous renseigner sur les livres, leurs auteurs, leurs prix et leur disponibilité.</li>
    </ol>
    <form class="catalogue-assistant-form" id="catalogue-assistant-form">
        <label for="catalogue-assistant-input" class="sr-only">Votre question</label>
        <input id="catalogue-assistant-input" name="message" maxlength="1000" required autocomplete="off" placeholder="Écrivez votre question…">
        <button type="submit">Envoyer</button>
    </form>
    <p class="catalogue-assistant-privacy">Les messages ne sont pas enregistrés par AGAU Éditions. Ils sont transmis à OpenAI pour générer la réponse; évitez d’inclure des informations personnelles.</p>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const launcher = document.getElementById('catalogue-assistant-launcher');
        const panel = document.getElementById('catalogue-assistant-panel');
        const closeButton = document.getElementById('catalogue-assistant-close');
        const form = document.getElementById('catalogue-assistant-form');
        const input = document.getElementById('catalogue-assistant-input');
        const messages = document.getElementById('catalogue-assistant-messages');

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

            try {
                const response = await fetch(@json(route('public-assistant.ask')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: message })
                });
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'La demande n’a pas pu être traitée.');
                }

                addMessage(result.answer, false);
            } catch (error) {
                addMessage(error.message || 'L’assistant est momentanément indisponible. Réessayez plus tard.', false);
            } finally {
                submitButton.disabled = false;
                input.focus();
            }
        });
    });
</script>
