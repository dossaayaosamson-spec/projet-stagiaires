<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Author;
use App\Models\Contract;
use App\Models\Manuscript;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManuscriptAdminController extends Controller
{
    /**
     * Liste des manuscrits reçus
     */
    public function index(Request $request): View
    {
        $query = Manuscript::with(['user', 'contract'])->latest('submitted_at');

        // Filtrer par provenance (candidature_auteur, lecthor_public, auteur_contrat)
        if ($request->filled('source')) {
            $query->where('submission_source', $request->source);
        }

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('status', $request->statut);
        }

        // Filtrer par catégorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Recherche par titre, nom d'auteur ou email d'utilisateur
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('pen_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('prenom', 'like', "%{$search}%")
                            ->orWhere('nom_famille', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $manuscripts = $query->paginate(15)->withQueryString();

        return view('admin.manuscripts.index', compact('manuscripts'));
    }

    /**
     * Détails d'un manuscrit
     */
    public function show(Manuscript $manuscript): View
    {
        $manuscript->load(['user.author', 'contract']);

        // Action logs liés à ce manuscrit
        $logs = AdminActionLog::where('target_type', Manuscript::class)
            ->where('target_id', $manuscript->id)
            ->with('admin')
            ->latest()
            ->get();

        return view('admin.manuscripts.show', compact('manuscript', 'logs'));
    }

    /**
     * Mettre à jour le statut du manuscrit et les notes de l'admin
     */
    public function update(Request $request, Manuscript $manuscript): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,analyzed,accepted,rejected',
            'admin_notes' => 'nullable|string|max:2000',
            // Champs optionnels lors d'une acceptation de candidature auteur
            'type_contrat' => 'nullable|string|in:compte_editeur,distribution_numerique,autre',
            'taux_redevance' => 'nullable|numeric|min:0|max:100',
        ]);

        $oldStatus = $manuscript->status;

        DB::transaction(function () use ($manuscript, $data) {
            $manuscript->update([
                'status' => $data['status'],
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            // Si c'est une candidature auteur acceptée et qu'un utilisateur est lié
            if ($data['status'] === 'accepted' && $manuscript->user) {
                $user = $manuscript->user;

                // 1. Promouvoir le rôle utilisateur en 'auteur'
                if ($user->role !== 'admin') {
                    $user->role = 'auteur';
                    $user->save();
                }

                // 2. Créer ou récupérer le profil Auteur
                $author = $user->author;
                if (! $author) {
                    $nomAuteur = $manuscript->pen_name ?: ($manuscript->author_name ?: $user->nom_complet);
                    $initiales = strtoupper(substr($user->prenom, 0, 1).substr($user->nom_famille, 0, 1));
                    $author = Author::create([
                        'user_id' => $user->id,
                        'nom' => $nomAuteur,
                        'initiales' => $initiales ?: 'AU',
                        'bio' => $manuscript->bio,
                        'avatar' => $manuscript->portrait_path,
                    ]);
                }

                // 3. Créer le contrat initial s'il n'existe pas encore
                if (! $manuscript->contract) {
                    $typeContrat = $data['type_contrat'] ?? 'compte_editeur';
                    $tauxRedevance = isset($data['taux_redevance']) && $data['taux_redevance'] !== null
                        ? (float) $data['taux_redevance']
                        : ($typeContrat === 'compte_editeur' ? 10.00 : 50.00);

                    Contract::create([
                        'author_id' => $author->id,
                        'manuscript_id' => $manuscript->id,
                        'book_id' => null, // Sera rattaché lors de la publication du livre
                        'type_contrat' => $typeContrat,
                        'taux_redevance' => $tauxRedevance,
                        'date_debut' => now()->toDateString(),
                        'statut' => 'actif',
                    ]);
                }
            }
        });

        // Journaliser l'action d'administration
        AdminActionLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_manuscript_status',
            'target_type' => Manuscript::class,
            'target_id' => $manuscript->id,
            'details' => [
                'old_status' => $oldStatus,
                'new_status' => $data['status'],
                'notes' => $data['admin_notes'] ?? null,
            ],
        ]);

        return redirect()->route('admin.manuscripts.show', $manuscript)
            ->with('success', 'Le statut du manuscrit a été mis à jour avec succès.');
    }

    /**
     * Télécharger le fichier du manuscrit
     */
    public function download(Manuscript $manuscript): StreamedResponse|RedirectResponse
    {
        if ($manuscript->file_path && Storage::disk('public')->exists($manuscript->file_path)) {
            return Storage::disk('public')->download($manuscript->file_path);
        }

        return redirect()->back()->with('error', 'Le fichier du manuscrit est introuvable.');
    }

    /**
     * Supprimer un manuscrit
     */
    public function destroy(Manuscript $manuscript): RedirectResponse
    {
        // Supprimer le fichier
        if ($manuscript->file_path && Storage::disk('public')->exists($manuscript->file_path)) {
            Storage::disk('public')->delete($manuscript->file_path);
        }

        // Journalisation de la suppression
        AdminActionLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_manuscript',
            'target_type' => Manuscript::class,
            'target_id' => $manuscript->id,
            'details' => [
                'title' => $manuscript->title,
                'author_name' => $manuscript->author_name,
            ],
        ]);

        $manuscript->delete();

        return redirect()->route('admin.manuscripts.index')
            ->with('success', 'Le manuscrit a été supprimé avec succès.');
    }
}
