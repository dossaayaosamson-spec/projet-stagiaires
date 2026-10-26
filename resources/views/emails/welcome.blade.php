<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez AGAU Éditions</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafb; color: #1a1a2e; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background-color: #0A3050; padding: 40px; text-align: center; }
        .header img { width: 80px; }
        .content { padding: 40px; line-height: 1.6; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 0.8rem; color: #64748b; }
        .button { display: inline-block; padding: 14px 28px; background-color: #1B6CA8; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        h1 { font-family: 'Georgia', serif; color: #0A3050; font-size: 24px; }
        .highlight { color: #1B6CA8; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU Éditions">
        </div>
        <div class="content">
            <h1>Bienvenue, <span class="highlight">{{ $user->name }}</span> !</h1>
            <p>Nous sommes ravis de vous compter parmi nos lecteurs. Chez <span class="highlight">AGAU Éditions</span>, notre mission est de célébrer et de diffuser la richesse de la littérature africaine contemporaine.</p>
            <p>Votre compte a été créé avec succès. Vous pouvez désormais :</p>
            <ul>
                <li>Parcourir notre catalogue de livres uniques</li>
                <li>Lire des <strong>Short Stories</strong> exclusives</li>
                <li>Souscrire à nos offres d'abonnement pour un accès illimité</li>
            </ul>
            <p>Commencez votre voyage littéraire dès maintenant :</p>
            <div style="text-align: center;">
                <a href="{{ route('home') }}" class="button">Explorer la boutique</a>
            </div>
            <p style="margin-top: 30px;">À très bientôt entre les pages,</p>
            <p><strong>L'équipe AGAU Éditions</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions. Tous droits réservés.<br>
            Lomé, Togo.
        </div>
    </div>
</body>
</html>
