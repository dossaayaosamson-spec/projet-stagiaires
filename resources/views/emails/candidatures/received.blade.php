<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle candidature eCLA 2026</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafb; color: #1a1a2e; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background-color: #0A3050; padding: 40px; text-align: center; }
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
            <span style="color: white; font-family: 'Georgia', serif; font-size: 24px; font-weight: bold;">Administration - AGAU Éditions</span>
        </div>
        <div class="content">
            <h1>Nouvelle candidature reçue pour eCLA 2026</h1>
            <p>Une nouvelle candidature a été soumise avec succès pour le concours.</p>
            
            <div class="card">
                <strong>Informations du candidat :</strong><br>
                • <strong>Nom complet :</strong> {{ $candidature->prenoms }} {{ $candidature->nom }}<br>
                • <strong>Email :</strong> {{ $candidature->email }}<br>
                • <strong>WhatsApp :</strong> {{ $candidature->whatsapp }}<br>
                • <strong>Pays de résidence :</strong> {{ $candidature->pays_residence }} ({{ $candidature->ville_residence }})
            </div>

            <div class="card">
                <strong>Informations sur l'œuvre :</strong><br>
                • <strong>Titre :</strong> {{ $candidature->titre_manuscrit }}<br>
                • <strong>Genre :</strong> {{ $candidature->genre_litteraire }}<br>
                • <strong>Nombre de mots :</strong> {{ number_format($candidature->nombre_mots) }}
            </div>

            <p>Vous pouvez consulter et gérer cette candidature sur le tableau de bord d'administration des candidatures.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AGAU Éditions. Tous droits réservés.<br>
            Lomé, Togo.
        </div>
    </div>
</body>
</html>
