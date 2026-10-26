<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Contract;
use App\Models\Manuscript;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractAdminController extends Controller
{
    /**
     * Liste des contrats d'édition
     */
    public function index(Request $request): View
    {
        $query = Contract::with(['author.user', 'book', 'manuscript'])->latest('date_debut');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('author', function ($aq) use ($search) {
                    $aq->where('nom', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('titre', 'like', "%{$search}%");
                });
            });
        }

        $contracts = $query->paginate(15)->withQueryString();
        $authors = Author::orderBy('nom')->get();

        return view('admin.contracts.index', compact('contracts', 'authors'));
    }

    /**
     * Formulaire de création manuelle d'un contrat
     */
    public function create(): View
    {
        $authors = Author::orderBy('nom')->get();
        $books = Book::orderBy('titre')->get();
        $manuscripts = Manuscript::where('status', 'accepted')->latest()->get();

        return view('admin.contracts.create', compact('authors', 'books', 'manuscripts'));
    }

    /**
     * Enregistrement d'un contrat
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'author_id' => 'required|exists:authors,id',
            'book_id' => 'nullable|exists:books,id',
            'manuscript_id' => 'nullable|exists:manuscripts,id',
            'type_contrat' => 'required|string|in:compte_editeur,distribution_numerique,autre',
            'taux_redevance' => 'required|numeric|min:0|max:100',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|string|in:brouillon,en_attente_signature,actif,resilie',
            'fichier_contrat_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('fichier_contrat_pdf')) {
            $data['fichier_contrat_pdf'] = $request->file('fichier_contrat_pdf')->store('contracts', 'public');
        }

        Contract::create($data);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contrat créé avec succès.');
    }

    /**
     * Édition d'un contrat
     */
    public function edit(Contract $contract): View
    {
        $contract->load(['author', 'book', 'manuscript']);
        $authors = Author::orderBy('nom')->get();
        $books = Book::orderBy('titre')->get();
        $manuscripts = Manuscript::latest()->get();

        return view('admin.contracts.edit', compact('contract', 'authors', 'books', 'manuscripts'));
    }

    /**
     * Mise à jour d'un contrat
     */
    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $data = $request->validate([
            'author_id' => 'required|exists:authors,id',
            'book_id' => 'nullable|exists:books,id',
            'manuscript_id' => 'nullable|exists:manuscripts,id',
            'type_contrat' => 'required|string|in:compte_editeur,distribution_numerique,autre',
            'taux_redevance' => 'required|numeric|min:0|max:100',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|string|in:brouillon,en_attente_signature,actif,resilie',
            'fichier_contrat_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('fichier_contrat_pdf')) {
            if ($contract->fichier_contrat_pdf && Storage::disk('public')->exists($contract->fichier_contrat_pdf)) {
                Storage::disk('public')->delete($contract->fichier_contrat_pdf);
            }
            $data['fichier_contrat_pdf'] = $request->file('fichier_contrat_pdf')->store('contracts', 'public');
        }

        $contract->update($data);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * Télécharger le PDF du contrat
     */
    public function downloadPdf(Contract $contract): StreamedResponse|RedirectResponse
    {
        if ($contract->fichier_contrat_pdf && Storage::disk('public')->exists($contract->fichier_contrat_pdf)) {
            return Storage::disk('public')->download($contract->fichier_contrat_pdf);
        }

        return redirect()->back()->with('error', 'Le document PDF du contrat est introuvable.');
    }

    /**
     * Supprimer un contrat
     */
    public function destroy(Contract $contract): RedirectResponse
    {
        if ($contract->fichier_contrat_pdf && Storage::disk('public')->exists($contract->fichier_contrat_pdf)) {
            Storage::disk('public')->delete($contract->fichier_contrat_pdf);
        }

        $contract->delete();

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contrat supprimé avec succès.');
    }
}
