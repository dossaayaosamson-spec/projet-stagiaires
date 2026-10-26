<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>La lecture vous manque ? — AGAU Éditions</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; color: #1e293b; margin: 0; padding: 30px 15px; }
        .card { max-width: 580px; margin: 0 auto; background: #ffffff; padding: 40px 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { height: 50px; }
        .greeting { font-size: 20px; font-weight: bold; color: #0f172a; margin-bottom: 16px; }
        .content-text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px; }
        .cta-container { text-align: center; margin: 30px 0; }
        .button { display: inline-block; padding: 14px 32px; background-color: #1b6ca8; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px; letter-spacing: 0.5px; text-transform: uppercase; box-shadow: 0 4px 12px rgba(27, 108, 168, 0.25); }
        .footer { border-top: 1px solid #e2e8f0; margin-top: 35px; padding-top: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @php
                $logoPath = public_path('images/AGAU LOGO BLEU.png');
                $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
            @endphp
            @if($logoData)
                <img src="data:image/png;base64,{{ $logoData }}" class="logo" alt="AGAU EDITIONS">
            @else
                <h2 style="color: #1b6ca8; margin: 0;">AGAU EDITIONS</h2>
            @endif
        </div>

        <div class="greeting">Bonjour {{ $user->prenom }},</div>

        <p class="content-text"><strong>La lecture vous manque ?</strong></p>

        <p class="content-text">
            Nous comprenons à quel point il est important de s'évader dans un bon livre. C'est pourquoi nous vous invitons à explorer notre vaste sélection de titres qui sauront captiver votre imagination.
        </p>

        <p class="content-text">
            Abonnez-vous dès aujourd'hui ou découvrez nos nouveaux ouvrages pour explorer de nouveaux horizons littéraires et profiter d'un accès illimité à notre bibliothèque.
        </p>

        <div class="cta-container">
            <a href="{{ route('front.plans') }}" class="button">Voir les offres</a>
        </div>

        <div class="footer">
            Cordialement,<br>
            <strong>L'équipe AGAU-EDITIONS</strong><br>
            <span style="font-size: 11px; color: #cbd5e1;">Lomé, Togo • www.agau-editions.com</span>
        </div>
    </div>
</body>
</html>
