<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmed;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Services\RoyaltyService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.book']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'statut' => 'required|string|in:en_attente,en_attente_transfert,en_attente_verification,confirmée,en_preparation,en_route,livrée,annulée',
        ]);

        $previousStatus = $order->statut;
        $order->statut = $request->statut;
        $order->save();

        if (in_array($order->statut, ['confirmée', 'livrée']) && ! in_array($previousStatus, ['confirmée', 'livrée'])) {
            app(RoyaltyService::class)->processOrderRoyalties($order);
        }

        if ($order->statut === 'confirmée' && $previousStatus !== 'confirmée' && $order->user?->email) {
            try {
                Mail::to($order->user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                Log::error('Erreur envoi email confirmation commande: '.$e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès (nouveau statut : '.str_replace('_', ' ', $order->statut).').');
    }

    public function approveProof(Request $request, Order $order)
    {
        $previousStatus = $order->statut;
        $order->statut = 'confirmée';
        $order->save();

        if (! in_array($previousStatus, ['confirmée', 'livrée'])) {
            app(RoyaltyService::class)->processOrderRoyalties($order);
        }

        if ($order->user?->email) {
            try {
                Mail::to($order->user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                Log::error('Erreur envoi email confirmation commande: '.$e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Preuve validée avec succès et commande passée au statut confirmée.');
    }

    public function rejectProof(Request $request, Order $order)
    {
        $order->statut = 'annulée';
        $order->save();

        return redirect()->back()->with('error', 'Preuve de paiement rejetée. La commande a été annulée.');
    }

    /**
     * Expédier une commande physique : enregistre les infos du livreur,
     * passe le statut à en_route et notifie le client par e-mail.
     */
    public function ship(Request $request, Order $order)
    {
        $request->validate([
            'nom_livreur' => ['required', 'string', 'max:255'],
            'telephone_livreur' => ['required', 'string', 'max:30'],
            'frais_livraison' => ['required', 'numeric', 'min:500'],
        ]);

        $order->nom_livreur = $request->nom_livreur;
        $order->telephone_livreur = $request->telephone_livreur;
        $order->frais_livraison = $request->frais_livraison;
        $order->total = $order->items->sum(fn ($i) => $i->prix_unitaire * $i->quantite) + $request->frais_livraison;
        $order->statut = 'en_route';
        $order->save();

        if ($order->user?->email) {
            try {
                Mail::to($order->user->email)->send(new OrderShipped($order));
            } catch (\Exception $e) {
                Log::error('Erreur envoi email expédition commande #'.$order->id.': '.$e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Commande expédiée avec succès. Le client a été notifié par e-mail.');
    }

    public function showProof(Order $order)
    {
        if (! $order->transaction_proof) {
            abort(404, 'Aucune preuve de paiement associée à cette commande.');
        }

        // Vérifier d'abord sur le stockage privé (local), puis sur le stockage public pour rétro-compatibilité
        $disk = Storage::disk('local')->exists($order->transaction_proof) ? 'local' : 'public';

        if (! Storage::disk($disk)->exists($order->transaction_proof)) {
            abort(404, 'Le fichier de preuve de paiement est introuvable.');
        }

        return Storage::disk($disk)->response($order->transaction_proof);
    }

    public function destroy(Order $order)
    {
        Order::destroy($order->id);

        return redirect()->route('admin.orders.index')->with('success', 'Commande supprimée avec succès.');
    }

    /**
     * Génère et télécharge la facture officielle au format PDF.
     */
    public function downloadInvoice(Order $order)
    {
        $order->load(['user', 'items.book']);

        $pdf = Pdf::loadView('pdf.invoice', compact('order'));

        $fileName = 'Facture_AGAU_FACT-'.date('Ym', strtotime($order->created_at)).'-'.str_pad($order->id, 5, '0', STR_PAD_LEFT).'.pdf';

        return $pdf->download($fileName);
    }
}
