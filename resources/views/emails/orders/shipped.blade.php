<x-mail::message>
# Votre commande est en route ! 🚚

Bonjour **{{ $order->user->name ?? $order->nom_destinataire }}**,

Bonne nouvelle ! Votre commande **#{{ $order->id }}** a été prise en charge et est actuellement en cours de livraison.

---

## 📦 Récapitulatif de la commande

@foreach ($order->items as $item)
- **{{ $item->book->titre ?? 'Livre' }}** — {{ $item->quantite }} ex. × {{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA
@endforeach

| | |
|---|---|
| Sous-total livres | {{ number_format($order->total - ($order->frais_livraison ?? 0), 0, ',', ' ') }} FCFA |
| Frais de livraison | {{ number_format($order->frais_livraison ?? 0, 0, ',', ' ') }} FCFA |
| **Total** | **{{ number_format($order->total, 0, ',', ' ') }} FCFA** |

---

## 📍 Adresse de livraison

**{{ $order->nom_destinataire }}**
{{ $order->adresse_livraison }}, {{ $order->ville_livraison }}
{{ $order->pays_livraison }}
Tél : {{ $order->telephone_livraison }}

---

## 🤝 Coordonnées de votre livreur

Votre livreur prendra contact avec vous pour organiser la remise du colis.

| | |
|---|---|
| **Nom** | {{ $order->nom_livreur }} |
| **Téléphone** | {{ $order->telephone_livreur }} |

@if ($order->notes_livraison)
> **Note :** {{ $order->notes_livraison }}
@endif

---

En cas de problème, n'hésitez pas à nous contacter.

Merci pour votre confiance,<br>
**L'équipe AGAU Éditions**

<x-mail::button :url="route('client.orders')">
Voir mes commandes
</x-mail::button>
</x-mail::message>
