<?php

namespace App\Http\Controllers;

use App\Services\AIPublishingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentDocumentController extends Controller
{
    protected $aiService;

    public function __construct(AIPublishingService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Initier un paiement
     */
    public function initiate(Request $request)
    {
        try {
            $manuscriptId = $request->input('manuscript_id');
            $format = $request->input('format', 'pdf');
            $result = $this->aiService->initiatePayment($manuscriptId, $format);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Initiation paiement échouée : '.$e->getMessage());

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function status($transactionId)
    {
        $result = $this->aiService->checkPaymentStatus($transactionId);
        if (! $result) {
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        return response()->json($result);
    }

    /**
     * Télécharger le document
     */
    public function download(Request $request, $manuscriptId)
    {
        $token = $request->query('token');
        $format = $request->query('format', 'pdf'); // format optionnel (sera ignoré si présent)
        if (! $token) {
            abort(400, 'Token de téléchargement manquant');
        }
        $response = $this->aiService->downloadDocument($manuscriptId, $token, $format);
        if ($response->failed()) {
            abort(404, 'Document non trouvé ou lien expiré');
        }

        return response(
            $response->body(),
            $response->status(),
            [
                'Content-Type' => $response->header('Content-Type'),
                'Content-Disposition' => $response->header('Content-Disposition'),
            ]
        );
    }

    /**
     * Page de succès après paiement (redirigée par FedaPay)
     */
    public function paymentSuccess(Request $request)
    {
        $fedapayId = $request->query('id') ?? $request->query('transaction_id');
        if (! $fedapayId) {
            return redirect('/')->with('error', 'Identifiant de transaction manquant.');
        }

        $djangoUrl = config('services.django.url', 'https://agaueditions-ia.saeicubetech.com');
        try {
            $response = Http::timeout(5)->get($djangoUrl.'/api/verify-payment/', [
                'fedapay_id' => $fedapayId,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur appel verify-payment: '.$e->getMessage());

            return redirect('/')->with('error', 'Erreur lors de la vérification du paiement.');
        }

        if ($response->successful() && $response->json('status') === 'paid') {
            $token = $response->json('download_token');
            $manuscriptId = $response->json('manuscript_id');
            // Pas de paramètre format – le format est déjà dans la base
            $downloadUrl = url("/download/{$manuscriptId}").'?'.http_build_query(['token' => $token]);

            return redirect($downloadUrl);
        }

        // En cas d'attente (webhook pas encore exécuté)
        return view('success', ['fedapayId' => $fedapayId]);
    }
}
