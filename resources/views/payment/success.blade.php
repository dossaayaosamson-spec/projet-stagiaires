<!DOCTYPE html>
<html>
<head>
    <title>Paiement confirmé</title>
    <meta http-equiv="refresh" content="3;url={{ url('/payment/success?id=' . $fedapayId) }}">
</head>
<body>
    <h1>Merci pour votre paiement !</h1>
    <p>Votre rapport est en cours de préparation. Vous allez être redirigé automatiquement.</p>
    <p>Si rien ne se passe, <a href="{{ url('/payment/success?id=' . $fedapayId) }}">cliquez ici</a>.</p>
</body>
</html>