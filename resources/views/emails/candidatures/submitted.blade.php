<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature eCLA 2026</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafb; color: #1a1a2e; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background-color: #0A3050; padding: 40px; text-align: center; }
        .header img { width: 80px; }
        .content { padding: 40px; line-height: 1.6; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 0.8rem; color: #64748b; }
        .highlight { color: #1B6CA8; font-weight: bold; }
        h1 { font-family: 'Georgia', serif; color: #0A3050; font-size: 24px; }
        .card { background-color: #f8fafc; border-left: 4px solid #1B6CA8; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span style="color: white; font-family: 'Georgia', serif; font-size: 24px; font-weight: bold;">AGAU Éditions</span>
        </div>
        <div class="content">
            <h1>Confirmation de réception de votre candidature</h1>
            <p>Bonjour <span class="highlight">{{ $candidature->prenoms }} {{ $candidature->nom }}</span>,</p>
            <p>Nous vous remercions pour votre participation au <strong>Concours Littéraire et d'Écriture AGAU eCLA 2026</strong>.</p>
            <p>Nous avons bien reçu votre manuscrit ainsi que les informations relatives à votre candidature :</p>
            
            <div class="card">
                <strong>Détails du projet soumis :</strong><br>
                • <strong>Titre de l'œuvre :</strong> {{ $candidature->titre_manuscrit }}<br>
                • <strong>Genre littéraire :</strong> {{ $candidature->genre_litteraire }}<br>
                • <strong>Nombre de mots :</strong> {{ number_format($candidature->nombre_mots) }} mots
            </div>

            <p>Votre candidature est actuellement en attente d'évaluation par notre comité de lecture. Vous serez informé(e) par email de l'évolution de son statut.</p>
            <p>Nous vous souhaitons bonne chance pour ce concours !</p>
            
            <p style="margin-top: 30px;">Cordialement,</p>
            <p><strong>L'équipe AGAU Éditions</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions. Tous droits réservés.<br>
            Lomé, Togo.
        </div>
    </div>
</body>
</html>
