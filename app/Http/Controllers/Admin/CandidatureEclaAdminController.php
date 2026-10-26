<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidatureEcla;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidatureEclaAdminController extends Controller
{
    /**
     * Liste des candidatures
     */
    public function index(Request $request): View
    {
        $query = CandidatureEcla::latest();

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer par genre littéraire
        if ($request->filled('genre_litteraire')) {
            $query->where('genre_litteraire', $request->genre_litteraire);
        }

        // Recherche par nom/prénoms ou titre de l'œuvre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenoms', 'like', "%{$search}%")
                    ->orWhere('titre_manuscrit', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $candidatures = $query->paginate(15)->withQueryString();

        return view('admin.candidatures.index', compact('candidatures'));
    }

    /**
     * Afficher les détails d'une candidature
     */
    public function show(CandidatureEcla $candidature): View
    {
        return view('admin.candidatures.show', compact('candidature'));
    }

    /**
     * Mettre à jour le statut et ajouter des notes
     */
    public function update(Request $request, CandidatureEcla $candidature): RedirectResponse
    {
        $data = $request->validate([
            'statut' => 'required|string|in:en_attente,en_cours,acceptee,refusee',
            'notes_admin' => 'nullable|string',
        ]);

        $candidature->update($data);

        return redirect()->route('admin.candidatures.show', $candidature)
            ->with('success', 'La candidature a été mise à jour avec succès.');
    }

    /**
     * Télécharger le manuscrit de manière sécurisée
     */
    public function download(CandidatureEcla $candidature): StreamedResponse|RedirectResponse
    {
        if (Storage::disk('public')->exists($candidature->fichier_manuscrit)) {
            return Storage::disk('public')->download($candidature->fichier_manuscrit);
        }

        return redirect()->back()->with('error', 'Le fichier du manuscrit est introuvable.');
    }

    /**
     * Supprimer une candidature
     */
    public function destroy(CandidatureEcla $candidature): RedirectResponse
    {
        // Supprimer le fichier associé
        if (Storage::disk('public')->exists($candidature->fichier_manuscrit)) {
            Storage::disk('public')->delete($candidature->fichier_manuscrit);
        }

        $candidature->delete();

        return redirect()->route('admin.candidatures.index')
            ->with('success', 'La candidature a été supprimée avec succès.');
    }
}
