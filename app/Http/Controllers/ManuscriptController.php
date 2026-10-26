<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use App\Services\AIPublishingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManuscriptController extends Controller
{
    protected $aiService;

    public function __construct(AIPublishingService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function create()
    {
        return view('manuscripts.create');
    }

    /**
     * Soumet le manuscrit à Django pour analyse et redirige vers les résultats.
     * Laravel ne stocke que les métadonnées — tout l'analyse reste dans Django.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,docx,doc,txt|max:20480',
            'author_name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:255',
            'synopsis' => 'nullable|string|max:2000',
        ]);

        $file = $request->file('document');
        $path = $file->store('manuscripts', 'public');

        $authorName = $request->author_name;
        if (empty($authorName) && auth()->check()) {
            $authorName = auth()->user()->nom_complet;
        }

        $source = (auth()->check() && auth()->user()->role === 'auteur')
            ? 'auteur_contrat'
            : 'lecthor_public';

        $manuscript = Manuscript::create([
            'user_id' => auth()->id(),
            'submission_source' => $source,
            'title' => $request->title ?? 'Sans titre',
            'category' => $request->category,
            'synopsis' => $request->synopsis,
            'author_name' => $authorName ?? 'Auteur',
            'file_path' => $path,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        try {
            $result = $this->aiService->analyzeManuscript(
                $request->file('document'),
                $authorName ?? 'Auteur',
                $manuscript->id
            );

            $manuscript->update([
                'status' => 'analyzed',
                'analyzed_at' => now(),
            ]);

            Log::info('Manuscrit analysé avec succès', [
                'manuscript_id' => $manuscript->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('manuscripts.results', ['id' => $manuscript->id])
                ->with('success', 'Votre manuscrit a bien été soumis et analysé.');

        } catch (\Exception $e) {
            Log::error('Erreur soumission manuscrit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $manuscript->update([
                'status' => 'pending',
                'analyzed_at' => null,
            ]);

            return redirect()
                ->route('manuscripts.create')
                ->with('error', 'Votre manuscrit a été enregistré, mais une erreur s\'est produite. Veuillez réessayer ou contacter l\'équipe.');
        }
    }

    /**
     * Affiche les résultats en laissant le JavaScript charger les données depuis Django.
     * Le token optionnel est passé à la vue pour permettre l'accès complet après paiement.
     */
    public function results($id, Request $request)
    {
        $manuscript = Manuscript::findOrFail($id);
        $token = $request->query('token'); // Récupère ?token=xxx

        return view('manuscripts.results', [
            'manuscript' => $manuscript,
            'access_token' => $token,
        ]);
    }

    /**
     * Construit le tableau $analysis unifié pour results.blade.php.
     * Accepte la réponse plate de Django (tous les champs à la racine).
     * (Conservé pour d'éventuels usages, mais non utilisé dans la vue dynamique)
     */
    private function buildAnalysisArray(array $d, Manuscript $manuscript): array
    {
        $styleAnalysis = $this->toArray($d['style_analysis'] ?? []);
        $coherenceAnalysis = $this->toArray($d['coherence_analysis'] ?? []);
        $authorStyleCheck = $this->toArray($d['author_style_check'] ?? []);
        $globalAssessment = $this->toArray($d['global_assessment'] ?? []);

        $analysis = [
            'summary' => $d['summary'] ?? 'Résumé non disponible.',
            'conclusion' => $d['conclusion'] ?? 'Conclusion non disponible.',
            'overall_score' => (int) ($d['overall_score'] ?? 0),
            'created_at' => $d['created_at'] ?? ($manuscript->analyzed_at ?? $manuscript->submitted_at),
            'strengths' => $this->toArray($d['strengths'] ?? []),
            'weaknesses' => $this->toArray($d['weaknesses'] ?? []),
            'recommendations_for_author' => $this->toArray($d['recommendations_for_author'] ?? $d['recommendations'] ?? []),
            'detailed_corrections' => $this->toArray($d['detailed_corrections'] ?? []),
            'rewrite_suggestions' => $this->toArray($d['rewrite_suggestions'] ?? []),
            'style_analysis' => $styleAnalysis,
            'coherence_analysis' => $coherenceAnalysis,
            'author_style_check' => $authorStyleCheck,
            'global_assessment' => $globalAssessment,
            'analysis_metadata' => $this->toArray($d['analysis_metadata'] ?? []),
            'style_score' => $authorStyleCheck['consistency_score'] ?? $styleAnalysis['consistency_score'] ?? 75,
            'style_observed' => $authorStyleCheck['summary'] ?? $styleAnalysis['summary'] ?? '',
            'style_summary' => $authorStyleCheck['summary'] ?? $styleAnalysis['summary'] ?? '',
            'style_deviations' => $this->toArray($authorStyleCheck['deviations'] ?? []),
            'style_recs' => $this->toArray($authorStyleCheck['recommendations'] ?? []),
            'style_strengths' => $this->toArray($styleAnalysis['strong_points'] ?? []),
            'style_improvements' => $this->toArray($styleAnalysis['improvements'] ?? []),
            'temporal_issues' => $this->toArray($coherenceAnalysis['temporal_issues'] ?? []),
            'character_consistency' => $this->toArray($coherenceAnalysis['character_consistency'] ?? []),
            'logic_issues' => $this->toArray($coherenceAnalysis['logic_issues'] ?? []),
            'unresolved' => $this->toArray($coherenceAnalysis['unresolved_points'] ?? []),
        ];

        $arrayFields = [
            'strengths', 'weaknesses', 'recommendations_for_author',
            'detailed_corrections', 'rewrite_suggestions',
            'style_strengths', 'style_improvements',
            'style_deviations', 'style_recs',
            'temporal_issues', 'character_consistency',
            'logic_issues', 'unresolved',
        ];
        foreach ($arrayFields as $field) {
            if (! isset($analysis[$field]) || ! is_array($analysis[$field])) {
                $analysis[$field] = [];
            }
        }

        return $analysis;
    }

    /**
     * Retourne un tableau vide structuré quand Django est injoignable.
     */
    private function emptyAnalysis(Manuscript $manuscript): array
    {
        return [
            'summary' => 'Données non disponibles.',
            'conclusion' => 'Données non disponibles.',
            'overall_score' => 0,
            'created_at' => $manuscript->analyzed_at ?? $manuscript->submitted_at,
            'strengths' => [],
            'weaknesses' => [],
            'recommendations_for_author' => [],
            'detailed_corrections' => [],
            'rewrite_suggestions' => [],
            'style_analysis' => [],
            'coherence_analysis' => [],
            'author_style_check' => [],
            'global_assessment' => [],
            'analysis_metadata' => [],
            'style_score' => 0,
            'style_observed' => '',
            'style_summary' => '',
            'style_deviations' => [],
            'style_recs' => [],
            'style_strengths' => [],
            'style_improvements' => [],
            'temporal_issues' => [],
            'character_consistency' => [],
            'logic_issues' => [],
            'unresolved' => [],
        ];
    }

    /**
     * Normalise une valeur en tableau.
     * Gère les cas : array, JSON string, null, objet stdClass.
     */
    private function toArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && ! empty($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }
        if (is_object($value)) {
            return (array) $value;
        }

        return [];
    }

    // ── Endpoints secondaires ──────────────────────────────────────────────

    public function apiAnalyze(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,docx,txt',
            'author_name' => 'nullable|string',
        ]);

        $response = $this->aiService->analyzeManuscript(
            $request->file('document'),
            $request->author_name,
            $request->manuscript_id
        );

        return response()->json($response);
    }

    public function getAnalysisFromDjango($manuscriptId)
    {
        $analysis = $this->aiService->getAnalysisByManuscriptId($manuscriptId);
        if (! $analysis) {
            return response()->json(['error' => 'Analyse non trouvée'], 404);
        }

        return response()->json($analysis);
    }

    /**
     * Endpoint de debug : affiche ce que Django retourne pour un manuscript_id donné.
     * À désactiver en production.
     */
    public function debugAnalysis($id)
    {
        $manuscript = Manuscript::findOrFail($id);

        $djangoData = null;
        $djangoError = null;

        try {
            $djangoData = $this->aiService->getAnalysisByManuscriptId($manuscript->id);
        } catch (\Exception $e) {
            $djangoError = $e->getMessage();
        }

        return response()->json([
            'laravel_manuscript_id' => $manuscript->id,
            'laravel_status' => $manuscript->status,
            'laravel_title' => $manuscript->title,
            'django_error' => $djangoError,
            'django_raw_keys' => is_array($djangoData) ? array_keys($djangoData) : null,
            'django_overall_score' => $djangoData['overall_score'] ?? null,
            'django_has_summary' => isset($djangoData['summary']),
            'django_has_strengths' => isset($djangoData['strengths']),
            'django_has_corrections' => isset($djangoData['detailed_corrections']),
            'django_full_response' => $djangoData,
        ]);
    }
}
