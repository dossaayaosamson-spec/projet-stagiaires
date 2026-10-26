@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU Éditions" style="width: 120px;">
</div>

# Bonjour {{ $prenom }} !

Merci de vous être inscrit sur **AGAU Éditions**, votre bibliothèque numérique dédiée au patrimoine littéraire africain.

Pour finaliser la création de votre compte et commencer votre lecture, veuillez utiliser le code de vérification
ci-dessous :

@component('mail::panel')
<div style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 10px; color: #1B6CA8;">
    {{ $code }}
</div>
@endcomponent

Ce code est valable pendant 5 minutes. Ne le partagez avec personne.

Si vous n'avez pas créé de compte sur notre plateforme, vous pouvez ignorer cet email en toute sécurité.

À bientôt sur AGAU Éditions,<br>
L'Équipe Editoriale
@endcomponent