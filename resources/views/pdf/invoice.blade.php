<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #FACT-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} — AGAU Éditions</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; }
        .header { width: 100%; border-bottom: 2px solid #1d4ed8; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { height: 55px; }
        .company-name { font-size: 20px; font-weight: bold; color: #1d4ed8; letter-spacing: 1px; margin: 0; }
        .company-tagline { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; }
        
        .invoice-title { font-size: 18px; font-weight: bold; color: #0f172a; text-align: right; text-transform: uppercase; margin: 0; }
        .invoice-details { font-size: 10px; color: #475569; text-align: right; margin-top: 5px; }

        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { width: 50%; vertical-align: top; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .box-title { font-size: 9px; font-weight: bold; color: #1d4ed8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 4px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #0f172a; color: #ffffff; text-align: left; padding: 8px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .items-table tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals-table { width: 45%; margin-left: auto; border-collapse: collapse; margin-bottom: 25px; }
        .totals-table td { padding: 6px 10px; font-size: 10px; }
        .totals-table tr.grand-total { background: #1d4ed8; color: #ffffff; font-weight: bold; font-size: 12px; }

        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .status-confirmée { background: #dcfce7; color: #15803d; }
        .status-en_route { background: #dbeafe; color: #1d4ed8; }
        .status-livrée { background: #f0fdf4; color: #166534; }
        .status-attente { background: #fef3c7; color: #b45309; }

        .footer { border-top: 1px solid #e2e8f0; padding-top: 12px; text-align: center; font-size: 8px; color: #94a3b8; margin-top: 30px; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/AGAU LOGO BLEU.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        $subtotal = $order->items->sum(fn($item) => $item->prix_unitaire * $item->quantite);
        $shipping = $order->frais_livraison ?? 0;
        $total = $order->total ?? ($subtotal + $shipping);
    @endphp

    <!-- En-tête -->
    <table class="header">
        <tr>
            <td style="width: 50%;">
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" class="logo" alt="AGAU Éditions">
                @else
                    <div class="company-name">AGAU ÉDITIONS</div>
                @endif
                <div class="company-tagline">Maison d'Édition & Plateforme Littéraire</div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="invoice-title">Facture</div>
                <div class="invoice-details">
                    <strong>N° :</strong> FACT-{{ date('Ym', strtotime($order->created_at)) }}-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}<br>
                    <strong>Statut :</strong> 
                    <span class="status-badge status-{{ $order->statut }}">
                        {{ str_replace('_', ' ', strtoupper($order->statut)) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Informations Client & Livraison -->
    <table class="info-table">
        <tr>
            <td style="padding-right: 10px;">
                <div class="box">
                    <div class="box-title">Client & Accquéreur</div>
                    <strong>{{ $order->user?->prenom }} {{ $order->user?->nom_famille }}</strong><br>
                    Email : {{ $order->user?->email }}<br>
                    Téléphone : {{ $order->user?->telephone ?? 'Non renseigné' }}<br>
                    Mode de paiement : <strong>{{ strtoupper($order->payment_method ?? 'FedaPay / Manuel') }}</strong>
                </div>
            </td>
            <td style="padding-left: 10px;">
                <div class="box">
                    <div class="box-title">Mode & Type de Commande</div>
                    <strong>Format :</strong> {{ $order->isPhysique() ? 'Livre Physique (Livraison)' : 'Numérique / E-book' }}<br>
                    @if($order->isPhysique())
                        <strong>Destinataire :</strong> {{ $order->nom_destinataire ?? $order->user?->prenom.' '.$order->user?->nom_famille }}<br>
                        <strong>Adresse :</strong> {{ $order->adresse_livraison ?? 'Lomé' }}<br>
                        <strong>Contact :</strong> {{ $order->telephone_livraison ?? $order->user?->telephone }}
                        @if($order->nom_livreur)
                            <br><strong>Livreur :</strong> {{ $order->nom_livreur }} ({{ $order->telephone_livreur }})
                        @endif
                    @else
                        <strong>Accès :</strong> Téléchargement immédiat dans la bibliothèque.
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Tableau des Articles -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Désignation / Article</th>
                <th style="width: 15%; text-align: center;">Format</th>
                <th style="width: 10%; text-align: center;">Qté</th>
                <th style="width: 10%; text-align: right;">P.U (FCFA)</th>
                <th style="width: 10%; text-align: right;">Total (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->book?->titre ?? 'Ouvrage' }}</strong>
                        @if($item->book?->auteur)
                            <br><span style="font-size: 8px; color: #64748b;">Par {{ $item->book->auteur }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $order->isPhysique() ? 'Exemplaire Papier' : 'Édition Numérique' }}
                    </td>
                    <td class="text-center">{{ $item->quantite }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Récapitulatif Totaux -->
    <table class="totals-table">
        <tr>
            <td>Sous-total :</td>
            <td class="text-right">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($order->isPhysique())
            <tr>
                <td>Frais d'expédition & livraison :</td>
                <td class="text-right">{{ number_format($shipping, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td style="padding: 8px 10px;">TOTAL NET À PAYER :</td>
            <td class="text-right" style="padding: 8px 10px;">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <!-- Pied de page -->
    <div class="footer">
        AGAU Éditions — Maison d'Édition & Diffusion Littéraire<br>
        Lomé, Togo • E-mail: contact@agau-editions.com • Web: www.agau-editions.com<br>
        Merci de votre confiance et de votre soutien à la création littéraire.
    </div>
</body>
</html>
