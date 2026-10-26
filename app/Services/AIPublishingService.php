<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class AIPublishingService
{
    protected $djangoApiUrl;

    public function __construct()
    {
        $this->djangoApiUrl = env('DJANGO_AI_API_URL', 'https://agaueditions-ia.saeicubetech.com');

    }

    // http://127.0.0.1:8001/
    // agaueditions-ia.saeicubetech.com
    /**
     * Analyser un manuscrit via Django
     * Endpoint: POST /api/analyze/
     */
    public function analyzeManuscript(UploadedFile $file, $authorName = null, $manuscriptId = null)
    {
        $response = Http::timeout(300) // 5 minutes
            ->retry(2, 1000) // 2 tentatives, délai 1 seconde
            ->attach('document', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post($this->djangoApiUrl.'/api/analyze/', [
                'author_name' => $authorName,
                'manuscript_id' => $manuscriptId,
            ]);

        if ($response->failed()) {
            throw new \Exception('Erreur analyse IA: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Récupérer une analyse par son ID (Django)
     * Endpoint: GET /api/status/{analysis_id}/
     */
    public function getAnalysisStatus($analysisId)
    {
        $response = Http::get($this->djangoApiUrl.'/api/status/'.$analysisId);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Récupérer une analyse par manuscript_id (Laravel)
     * Endpoint: GET /api/analysis/{manuscript_id}/
     *
     * Cette méthode est utile si vous voulez récupérer l'analyse sans avoir
     * à stocker l'ID Django, en utilisant l'ID que Laravel a envoyé.
     */
    public function getAnalysisByManuscriptId($manuscriptId)
    {
        $response = Http::get($this->djangoApiUrl.'/api/analysis/'.$manuscriptId);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Sauvegarder le profil de style d'un auteur
     * Endpoint: POST /api/style-profile/
     */
    public function saveAuthorStyleProfile($authorName, array $styleTraits = [], $tone = null, $previousWorksSummary = null)
    {
        $response = Http::post($this->djangoApiUrl.'/api/style-profile/', [
            'author_name' => $authorName,
            'style_traits' => $styleTraits,
            'tone' => $tone,
            'previous_works_summary' => $previousWorksSummary,
        ]);

        if ($response->failed()) {
            throw new \Exception('Erreur sauvegarde profil: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Récupérer le profil de style d'un auteur
     * Endpoint: GET /api/style-profile/{author_name}/
     */
    public function getAuthorStyleProfile($authorName)
    {
        $response = Http::get($this->djangoApiUrl.'/api/style-profile/'.urlencode($authorName));

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Initier un paiement pour télécharger l'analyse
     * Endpoint: POST /api/payment/initiate/
     */
    public function initiatePayment($manuscriptId, $format)
    {
        $response = Http::post($this->djangoApiUrl.'/api/payment/initiate/', [
            'manuscript_id' => $manuscriptId,
            'format' => $format,
        ]);

        if ($response->failed()) {
            throw new \Exception('Erreur initiation paiement: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Vérifier le statut d'un paiement
     * Endpoint: GET /api/payment/status/{transaction_id}/
     */
    public function checkPaymentStatus($transactionId)
    {
        $response = Http::get($this->djangoApiUrl.'/api/payment/status/'.$transactionId.'/');

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Télécharger le document via Django
     * Endpoint: GET /api/download/{manuscript_id}/
     */
    public function downloadDocument($manuscriptId, $token, $format)
    {
        $response = Http::get($this->djangoApiUrl.'/api/download/'.$manuscriptId.'/', [
            'token' => $token,
            'format' => $format,
        ]);

        return $response;
    }
}
