<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation d'abonnement - AGAU Éditions</title>
    <style>
        body { font-family: sans-serif; background-color: #f8fafb; color: #1a1a2e; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .plan-box { background: #EAF4FC; padding: 20px; border-radius: 8px; border-left: 4px solid #1B6CA8; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 0.8rem; color: #64748b; }
        .highlight { color: #1B6CA8; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU" width="70">
        </div>
        <h1>Félicitations pour votre abonnement !</h1>
        <p>Bonjour {{ $user->prenom }},</p>
        <p>Nous sommes heureux de vous confirmer que votre souscription à l'offre <span class="highlight">{{ $planName }}</span> a été activée avec succès.</p>
        
        <div class="plan-box">
            <strong>Détails de l'abonnement :</strong><br>
            Offre : {{ $planName }}<br>
            Date d'expiration : {{ $expiresAt->format('d/m/Y') }}
        </div>

        <p>Vous avez désormais un accès illimité à toute notre bibliothèque de livres numériques et à nos Short Stories exclusives pendant toute la durée de votre abonnement.</p>
        
        <p>Bonne lecture !</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions<br>
            Lomé, Togo.
        </div>
    </div>
</body>
</html>
