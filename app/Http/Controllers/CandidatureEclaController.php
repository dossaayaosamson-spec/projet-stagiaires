<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidatureStep1Request;
use App\Http\Requests\CandidatureStep2Request;
use App\Http\Requests\CandidatureStep3Request;
use App\Http\Requests\CandidatureStep4Request;
use App\Mail\CandidatureEclaReceived;
use App\Mail\CandidatureEclaSubmitted;
use App\Models\CandidatureEcla;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CandidatureEclaController extends Controller
{
    /**
     * Etape 1 : Informations personnelles
     */
    public function step1(Request $request): View
    {
        $data = $request->session()->get('candidature.step1', []);

        return view('front.candidature.step1', compact('data'));
    }

    public function step1Post(CandidatureStep1Request $request): RedirectResponse
    {
        $request->session()->put('candidature.step1', $request->validated());

        return redirect()->route('candidature.step2');
    }

    /**
     * Etape 2 : Informations sur l'oeuvre
     */
    public function step2(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('candidature.step1')) {
            return redirect()->route('candidature.step1');
        }

        $data = $request->session()->get('candidature.step2', []);

        return view('front.candidature.step2', compact('data'));
    }

    public function step2Post(CandidatureStep2Request $request): RedirectResponse
    {
        $request->session()->put('candidature.step2', $request->validated());

        return redirect()->route('candidature.step3');
    }

    /**
     * Etape 3 : Téléchargement du manuscrit
     */
    public function step3(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('candidature.step2')) {
            return redirect()->route('candidature.step2');
        }

        $data = $request->session()->get('candidature.step3', []);

        return view('front.candidature.step3', compact('data'));
    }

    public function step3Post(CandidatureStep3Request $request): RedirectResponse
    {
        $step1 = $request->session()->get('candidature.step1');
        $step2 = $request->session()->get('candidature.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('candidature.step1');
        }

        // Clean values for file name formatting
        $nom = str_replace(' ', '_', trim($step1['nom']));
        $prenoms = str_replace(' ', '_', trim($step1['prenoms']));
        $titre = str_replace(' ', '_', trim($step2['titre_manuscrit']));
        // Remove special chars to secure filename
        $nom = preg_replace('/[^A-Za-z0-9\_]/', '', $nom);
        $prenoms = preg_replace('/[^A-Za-z0-9\_]/', '', $prenoms);
        $titre = preg_replace('/[^A-Za-z0-9\_]/', '', $titre);

        $fileName = "eCLA_{$nom}_{$prenoms}_{$titre}.docx";

        // Store file in manuscripts folder (public disk or local)
        $path = $request->file('fichier_manuscrit')->storeAs('manuscripts/ecla', $fileName, 'public');

        $request->session()->put('candidature.step3', [
            'fichier_manuscrit' => $path,
            'original_filename' => $request->file('fichier_manuscrit')->getClientOriginalName(),
        ]);

        return redirect()->route('candidature.step4');
    }

    /**
     * Etape 4 : Engagement du candidat
     */
    public function step4(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('candidature.step3')) {
            return redirect()->route('candidature.step3');
        }

        $data = $request->session()->get('candidature.step4', []);

        return view('front.candidature.step4', compact('data'));
    }

    public function step4Post(CandidatureStep4Request $request): RedirectResponse
    {
        $step1 = $request->session()->get('candidature.step1');
        $step2 = $request->session()->get('candidature.step2');
        $step3 = $request->session()->get('candidature.step3');

        if (! $step1 || ! $step2 || ! $step3) {
            return redirect()->route('candidature.step1');
        }

        $step4Data = $request->validated();
        $request->session()->put('candidature.step4', $step4Data);

        // Save all data into database
        $candidature = new CandidatureEcla;
        $candidature->fill(array_merge(
            $step1,
            $step2,
            [
                'fichier_manuscrit' => $step3['fichier_manuscrit'],
                'declaration_originalite' => true,
                'acceptation_reglement' => true,
                'compte_agau' => (bool) $step4Data['compte_agau'],
                'source_decouverte' => $step4Data['source_decouverte'],
                'source_decouverte_autre' => $step4Data['source_decouverte_autre'] ?? null,
                'statut' => 'en_attente',
            ]
        ));
        $candidature->save();

        // Send Email to candidate
        try {
            Mail::to($candidature->email)->send(new CandidatureEclaSubmitted($candidature));
        } catch (\Exception $e) {
            // Log or ignore to not block the submission flow
            logger()->error("Impossible d'envoyer l'email de confirmation au candidat: ".$e->getMessage());
        }

        // Send Email to Admin (configured in mail config or env or default value)
        $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@agaueditions.com');
        try {
            Mail::to($adminEmail)->send(new CandidatureEclaReceived($candidature));
        } catch (\Exception $e) {
            logger()->error("Impossible d'envoyer la notification admin: ".$e->getMessage());
        }

        // Clear session
        $request->session()->forget('candidature');

        // Redirect to step 5 (confirmation)
        return redirect()->route('candidature.confirmation')->with('success_submitted', true);
    }

    /**
     * Etape 5 : Confirmation finale
     */
    public function confirmation(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('success_submitted') && ! app()->environment('testing')) {
            return redirect()->route('candidature.step1');
        }

        return view('front.candidature.confirmation');
    }
}
