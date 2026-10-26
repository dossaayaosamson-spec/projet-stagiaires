<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accusé de réception — AGAU Éditions</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafb; color: #1a1a2e; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background-color: #0A3050; padding: 32px 40px; text-align: center; }
        .header img { width: 70px; }
        .header-label { color: #7EC8F0; font-size: 0.7rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; }
        .content { padding: 36px 40px; line-height: 1.7; }
        .badge { display: inline-block; background-color: #EBF5FF; color: #1B6CA8; font-size: 0.7rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 12px; border-radius: 4px; margin-bottom: 20px; }
        h1 { font-family: 'Georgia', serif; color: #0A3050; font-size: 1.5rem; margin-top: 0; margin-bottom: 12px; }
        p { margin-top: 0; margin-bottom: 16px; font-size: 0.95rem; color: #334155; }
        .field { margin-bottom: 20px; }
        .field-label { font-size: 0.7rem; font-weight: 700; color: #64748b; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 4px; }
        .message-value { white-space: pre-wrap; font-size: 0.9rem; color: #334155; background: #f8fafb; border-left: 3px solid #1B6CA8; padding: 16px; border-radius: 0 6px 6px 0; line-height: 1.6; }
        .divider { height: 1px; background: #e8edf2; margin: 28px 0; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 0.75rem; color: #64748b; }
        .button { display: inline-block; padding: 10px 20px; background-color: #1B6CA8; color: white !important; text-decoration: none; border-radius: 4px; font-size: 0.8rem; font-weight: bold; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU Éditions">
            <div class="header-label">Confirmation de réception</div>
        </div>
        <div class="content">
            <span class="badge">Bonjour {{ $data['name'] }}</span>
            <h1>Nous avons bien reçu votre message !</h1>
            <p>Merci d'avoir contacté <strong>AGAU Éditions</strong>. Notre équipe éditoriale et de support a bien reçu votre demande concernant le sujet suivant : <strong style="color: #0A3050;">"{{ $data['subject'] }}"</strong>.</p>
            
            <p>Nous mettons tout en œuvre pour vous répondre dans les plus brefs délais, généralement sous 24 à 48 heures ouvrées.</p>

            <div class="divider"></div>

            <div class="field">
                <div class="field-label">Rappel de votre message :</div>
                <div class="message-value">{{ $data['message'] }}</div>
            </div>

            <div class="divider"></div>

            <p>En attendant notre retour, vous pouvez découvrir nos dernières parutions ou en savoir plus sur notre processus d'éditions :</p>
            <div style="text-align: center; margin-bottom: 10px;">
                <a href="{{ route('front.catalogue') }}" class="button">Découvrir nos livres</a>
                <a href="{{ route('front.contrat.editions') }}" class="button" style="background-color: #0A3050; margin-left: 10px;">Processus d'Éditions</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions — Lomé, Togo.<br>
            Ceci est un message automatique de confirmation de réception. Merci de ne pas y répondre directement.
        </div>
    </div>
</body>
</html>
