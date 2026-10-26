<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Code de vérification — AGAU Éditions</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 580px; margin: 40px auto; background: #ffffff; border-radius: 16px; padding: 40px; border: 1px solid #e2e8f0; shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { width: 90px; }
        .title { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 15px; margin-bottom: 10px; }
        .text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 25px; }
        .otp-box { background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 25px; }
        .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #1d4ed8; font-family: monospace; }
        .footer { text-align: center; font-size: 11px; color: #94a3b8; border-t: 1px solid #e2e8f0; padding-top: 20px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #1d4ed8; margin: 0;">AGAU ÉDITIONS</h2>
            <p style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-top: 4px;">Code d'authentification</p>
        </div>

        <p class="text">Bonjour {{ $prenom }},</p>
        <p class="text">Voici votre code de vérification à 6 chiffres pour valider votre connexion sur AGAU Éditions :</p>

        <div class="otp-box">
            <div class="otp-code">{{ $code }}</div>
        </div>

        <p class="text" style="font-size: 12px; color: #64748b;">
            Ce code est valide pendant <strong>10 minutes</strong>. Si vous n'avez pas demandé cette connexion, ignorez cet e-mail et sécurisez votre compte.
        </p>

        <div class="footer">
            <p>© {{ date('Y') }} AGAU Éditions. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
