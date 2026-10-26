<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre abonnement expire bientôt - AGAU Éditions</title>
    <style>
        body { font-family: sans-serif; background-color: #f8fafb; color: #1a1a2e; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 0.8rem; color: #64748b; }
        .button { display: inline-block; padding: 14px 28px; background-color: #1B6CA8; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .highlight { color: #e11d48; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU" width="70">
        </div>
        <h1>Votre voyage touche bientôt à sa fin...</h1>
        <p>Bonjour {{ $user->prenom }},</p>
        <p>Nous vous informons que votre abonnement <span class="highlight">{{ $subscription->plan }}</span> arrivera à expiration dans <span class="highlight">3 jours</span>, le {{ $subscription->date_fin->format('d/m/Y') }}.</p>
        
        <p>Pour continuer à profiter de l'accès illimité à nos ouvrages et ne pas interrompre vos lectures en cours, vous pouvez renouveler votre abonnement dès maintenant.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('front.plans') }}" class="button">Renouveler mon abonnement</a>
        </div>

        <p>Si vous avez des questions, notre équipe est à votre disposition.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions<br>
            Lomé, Togo.
        </div>
    </div>
</body>
</html>
