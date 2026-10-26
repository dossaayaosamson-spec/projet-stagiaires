<x-mail::message>
# Merci pour votre achat !

Bonjour {{ $order->user->prenom }},

Votre commande **#{{ $order->id }}** sur AGAU Éditions a été confirmée avec succès. 
Vos livres sont désormais disponibles dans votre bibliothèque en ligne.

<x-mail::table>
| Livre | Prix |
| :--- | :--- |
@foreach($order->items as $item)
| {{ $item->book->titre }} | {{ number_format($item->prix, 0, ',', ' ') }} FCFA |
@endforeach
| **Total** | **{{ number_format($order->total, 0, ',', ' ') }} FCFA** |
</x-mail::table>

<x-mail::button :url="route('client.library')">
Accéder à ma bibliothèque
</x-mail::button>

Si vous avez des questions concernant votre commande, n'hésitez pas à nous contacter.

Merci,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
