<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayoutAdminController extends Controller
{
    /**
     * Liste des demandes de retrait des auteurs
     */
    public function index(Request $request): View
    {
        $query = PayoutRequest::with(['author.user', 'validator'])->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('moyen_paiement')) {
            $query->where('moyen_paiement', $request->moyen_paiement);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_transaction', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($aq) use ($search) {
                        $aq->where('nom', 'like', "%{$search}%");
                    });
            });
        }

        $payouts = $query->paginate(15)->withQueryString();

        $stats = [
            'en_attente' => PayoutRequest::where('statut', 'en_attente')->count(),
            'montant_attente' => PayoutRequest::where('statut', 'en_attente')->sum('montant'),
            'total_verse' => PayoutRequest::where('statut', 'valide')->sum('montant'),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats'));
    }

    /**
     * Valider une demande de versement (décaissement)
     */
    public function approve(Request $request, PayoutRequest $payout): RedirectResponse
    {
        $data = $request->validate([
            'reference_transaction' => 'required|string|max:255',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($payout->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $author = $payout->author;

        if ($author->solde_disponible < $payout->montant) {
            return redirect()->back()->with('error', "Le solde disponible de l'auteur (".number_format($author->solde_disponible, 0, ',', ' ').' FCFA) est inférieur au montant demandé ('.number_format($payout->montant, 0, ',', ' ').' FCFA).');
        }

        DB::transaction(function () use ($payout, $author, $data) {
            // Débiter le solde disponible et incrémenter le total retiré
            $author->decrement('solde_disponible', $payout->montant);
            $author->increment('total_retire', $payout->montant);

            // Mettre à jour la demande
            $payout->update([
                'statut' => 'valide',
                'reference_transaction' => $data['reference_transaction'],
                'admin_notes' => $data['admin_notes'] ?? null,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);
        });

        return redirect()->route('admin.payouts.index')
            ->with('success', 'La demande de versement #'.$payout->id.' a été validée avec succès et le solde débité.');
    }

    /**
     * Rejeter une demande de versement
     */
    public function reject(Request $request, PayoutRequest $payout): RedirectResponse
    {
        $data = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        if ($payout->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $payout->update([
            'statut' => 'rejete',
            'admin_notes' => $data['admin_notes'],
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return redirect()->route('admin.payouts.index')
            ->with('success', 'La demande de versement #'.$payout->id.' a été rejetée.');
    }
}
