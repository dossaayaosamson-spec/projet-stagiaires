<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManualPaymentController extends Controller
{
    /**
     * Afficher la page d'instructions de transfert manuel
     */
    public function showInstructions(Order $order)
    {
        // Vérification de la propriété
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Accès refusé.');
        }

        // Vérification du statut
        if ($order->statut !== 'en_attente' && $order->statut !== 'en_attente_transfert') {
            return redirect()->route('client.orders')->with('warning', 'Cette commande ne peut plus être payée de cette manière.');
        }

        // Marquer la commande comme en attente de transfert (au cas où elle viendrait du panier)
        if ($order->statut === 'en_attente') {
            $order->statut = 'en_attente_transfert';
            $order->payment_method = 'transfert_manuel';
            $order->save();
        }

        // Vider le panier car la commande est prise en compte
        session()->forget('cart');

        return view('front.payment.manual', compact('order'));
    }

    /**
     * Traiter la soumission de la preuve de paiement
     */
    public function submitProof(Request $request, Order $order)
    {
        // Vérification de la propriété
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Accès refusé.');
        }

        $request->validate([
            'transaction_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ], [
            'transaction_proof.required' => 'Veuillez télécharger la capture d\'écran de votre reçu de transfert.',
            'transaction_proof.image' => 'Le fichier doit être une image (Capture d\'écran).',
            'transaction_proof.mimes' => 'Les formats acceptés sont jpeg, png, jpg.',
            'transaction_proof.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);

        try {
            if ($request->hasFile('transaction_proof')) {
                $file = $request->file('transaction_proof');
                $filename = 'proof_order_'.$order->id.'_'.time().'.'.$file->getClientOriginalExtension();

                $path = $file->storeAs('payments/proofs', $filename, 'public');

                $order->transaction_proof = $path;
                $order->statut = 'en_attente_verification';
                $order->save();

                return redirect()->route('client.orders')->with('success', 'Votre preuve de transfert a bien été envoyée ! Nous vérifierons votre paiement dans les plus brefs délais et mettrons à jour votre commande.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload de la preuve de paiement: '.$e->getMessage());

            return back()->with('error', 'Une erreur s\'est produite lors de l\'envoi de votre reçu. Veuillez réessayer.');
        }

        return back()->with('error', 'Aucun fichier reçu.');
    }
}
