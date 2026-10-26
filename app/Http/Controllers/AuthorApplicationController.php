<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthorApplicationController extends Controller
{
    /**
     * Affiche le formulaire de candidature "Devenir Auteur".
     */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAuthor()) {
            return redirect()->route('author.dashboard')
                ->with('info', 'Vous disposez déjà d\'un compte auteur officiel.');
        }

        // Vérifier si une candidature est déjà en cours
        $pendingApplication = $user->authorApplications()
            ->whereIn('status', ['pending', 'en_cours', 'en_examen'])
            ->latest('submitted_at')
            ->first();

        if ($pendingApplication) {
            return redirect()->route('client.become-author.status')
                ->with('info', 'Vous avez déjà un dossier de candidature en cours d\'examen.');
        }

        return view('client.become-author', compact('user'));
    }

    /**
     * Traite et enregistre le dossier de candidature "Devenir Auteur".
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAuthor()) {
            return redirect()->route('author.dashboard')
                ->with('info', 'Vous disposez déjà d\'un compte auteur officiel.');
        }

        // Empêcher la soumission multiple si un dossier est actif
        $pendingApplication = $user->authorApplications()
            ->whereIn('status', ['pending', 'en_cours', 'en_examen'])
            ->latest('submitted_at')
            ->first();

        if ($pendingApplication) {
            return redirect()->route('client.become-author.status')
                ->with('error', 'Un dossier de candidature est déjà en cours d\'examen.');
        }

        $validated = $request->validate([
            'pen_name' => ['nullable', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:30', 'max:3000'],
            'portrait' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'social_links' => ['nullable', 'string', 'max:500'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'synopsis' => ['required', 'string', 'min:50', 'max:5000'],
            'intent_note' => ['required', 'string', 'min:30', 'max:3000'],
            'manuscript_file' => ['required', 'file', 'mimes:pdf,docx,doc', 'max:20480'],
            'certified_original' => ['accepted'],
        ], [
            'bio.min' => 'Votre biographie doit comporter au moins 30 caractères.',
            'synopsis.min' => 'Le résumé / synopsis doit comporter au moins 50 caractères.',
            'intent_note.min' => 'La note d\'intention doit comporter au moins 30 caractères.',
            'manuscript_file.required' => 'Le fichier du manuscrit est obligatoire.',
            'manuscript_file.mimes' => 'Le manuscrit doit être un document PDF ou Word (.docx, .doc).',
            'manuscript_file.max' => 'La taille du manuscrit ne doit pas dépasser 20 Mo.',
            'certified_original.accepted' => 'Vous devez certifier sur l\'honneur être l\'auteur de cette œuvre originale.',
        ]);

        // Sauvegarde du manuscrit
        $filePath = $request->file('manuscript_file')->store('manuscripts/candidatures', 'public');

        // Sauvegarde éventuelle du portrait
        $portraitPath = null;
        if ($request->hasFile('portrait')) {
            $portraitPath = $request->file('portrait')->store('authors/portraits', 'public');
        }

        $authorName = ! empty($validated['pen_name'])
            ? $validated['pen_name']
            : trim(($user->prenom ?? '').' '.($user->nom_famille ?? ''));

        Manuscript::create([
            'user_id' => $user->id,
            'submission_source' => 'candidature_auteur',
            'title' => $validated['title'],
            'author_name' => $authorName,
            'pen_name' => $validated['pen_name'] ?? null,
            'category' => $validated['category'],
            'synopsis' => $validated['synopsis'],
            'bio' => $validated['bio'],
            'intent_note' => $validated['intent_note'],
            'file_path' => $filePath,
            'portrait_path' => $portraitPath,
            'city' => $validated['city'],
            'country' => $validated['country'],
            'social_links' => ! empty($validated['social_links']) ? ['url' => $validated['social_links']] : null,
            'certified_original' => true,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Mise à jour de l'alias si renseigné et non défini
        if (! empty($validated['pen_name']) && empty($user->alias)) {
            $user->update(['alias' => $validated['pen_name']]);
        }

        return redirect()->route('client.become-author.status')
            ->with('success', 'Votre dossier de candidature a été soumis avec succès ! Le comité éditorial va l\'étudier avec attention.');
    }

    /**
     * Affiche l'état d'avancement de la candidature auteur.
     */
    public function status(): View
    {
        $user = Auth::user();

        $candidatures = $user->authorApplications()
            ->latest('submitted_at')
            ->get();

        $latestCandidature = $candidatures->first();

        return view('client.become-author-status', compact('user', 'candidatures', 'latestCandidature'));
    }
}
