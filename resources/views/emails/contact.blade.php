<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact — AGAU Éditions</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafb; color: #1a1a2e; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background-color: #0A3050; padding: 32px 40px; text-align: center; }
        .header img { width: 70px; }
        .header-label { color: #7EC8F0; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; }
        .content { padding: 36px 40px; line-height: 1.7; }
        .badge { display: inline-block; background-color: #EBF5FF; color: #1B6CA8; font-size: 0.7rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 12px; border-radius: 4px; margin-bottom: 20px; }
        h1 { font-family: 'Georgia', serif; color: #0A3050; font-size: 1.5rem; margin-top: 0; margin-bottom: 4px; }
        .field { margin-bottom: 20px; }
        .field-label { font-size: 0.7rem; font-weight: 700; color: #64748b; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 4px; }
        .field-value { font-size: 0.95rem; color: #1a1a2e; background: #f8fafb; border-left: 3px solid #1B6CA8; padding: 12px 16px; border-radius: 0 6px 6px 0; }
        .message-value { white-space: pre-wrap; font-size: 0.95rem; color: #1a1a2e; background: #f8fafb; border-left: 3px solid #1B6CA8; padding: 16px; border-radius: 0 6px 6px 0; line-height: 1.7; }
        .reply-note { margin-top: 28px; padding: 14px 16px; background: #EBF5FF; border-radius: 6px; font-size: 0.82rem; color: #1B6CA8; }
        .reply-note a { color: #0A3050; font-weight: 700; }
        .divider { height: 1px; background: #e8edf2; margin: 28px 0; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 0.75rem; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU Éditions">
            <div class="header-label">Nouveau message de contact</div>
        </div>
        <div class="content">
            <span class="badge">📩 Formulaire de contact</span>
            <h1>Un visiteur vous a écrit</h1>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 0;">Reçu le {{ now()->translatedFormat('d F Y à H\hi') }}</p>

            <div class="divider"></div>

            <div class="field">
                <div class="field-label">Nom complet</div>
                <div class="field-value">{{ $data['name'] }}</div>
            </div>

            <div class="field">
                <div class="field-label">Adresse e-mail</div>
                <div class="field-value">
                    <a href="mailto:{{ $data['email'] }}" style="color: #1B6CA8; font-weight: 700;">{{ $data['email'] }}</a>
                </div>
            </div>

            <div class="field">
                <div class="field-label">Sujet</div>
                <div class="field-value">{{ $data['subject'] }}</div>
            </div>

            <div class="field">
                <div class="field-label">Message</div>
                <div class="message-value">{{ $data['message'] }}</div>
            </div>

            <div class="reply-note">
                💡 Pour répondre directement à <strong>{{ $data['name'] }}</strong>, cliquez sur <strong>Répondre</strong> dans votre messagerie ou écrivez à
                <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a>.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions — Lomé, Togo.<br>
            Ce message a été envoyé via le formulaire de contact du site <a href="{{ config('app.url') }}" style="color: #1B6CA8;">agaueditions.com</a>.
        </div>
    </div>
</body>
</html>
