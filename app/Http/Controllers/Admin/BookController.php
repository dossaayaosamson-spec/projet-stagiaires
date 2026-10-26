<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('author')->latest()->paginate(15);

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $authors = Author::with(['contracts' => function ($q) {
            $q->whereNull('book_id')->where('statut', 'actif');
        }])->orderBy('nom')->get();

        $unassignedContracts = \App\Models\Contract::whereNull('book_id')
            ->where('statut', 'actif')
            ->with('author')
            ->get();

        return view('admin.books.create', compact('authors', 'unassignedContracts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'categorie' => 'required|string|max:100',
            'resume' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:100',
            'disponibilite' => 'nullable|string|max:100',
            'maison_edition' => 'nullable|string|max:100',
            'couleur_couverture' => 'required|string|max:50',
            'prix' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_bestseller' => 'boolean',
            'is_free' => 'nullable|boolean',
            'free_until' => 'nullable|date',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
        ]);

        $contractId = $data['contract_id'] ?? null;
        unset($data['contract_id']);

        $data['is_bestseller'] = $request->has('is_bestseller');
        $data['is_free'] = $request->has('is_free');
        $data['free_until'] = $request->filled('free_until') ? $request->input('free_until') : null;

        // Gestion de l'image de couverture (Public)
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'books/covers/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            // Resize and crop to 400x600 (Standard book ratio 2:3)
            $image->cover(400, 600);

            // Save as WebP
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['cover_path'] = $path;
        }

        // Gestion du fichier PDF (Privé)
        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('books/pdfs');
            $data['pdf_path'] = $path;
        }

        $book = Book::create($data);

        // Lier le contrat choisi au livre
        if ($contractId) {
            \App\Models\Contract::where('id', $contractId)->update(['book_id' => $book->id]);
        } else {
            // Liaison automatique s'il y a un contrat en attente sans livre pour cet auteur
            $pendingContract = \App\Models\Contract::where('author_id', $book->author_id)
                ->whereNull('book_id')
                ->where('statut', 'actif')
                ->latest()
                ->first();

            if ($pendingContract) {
                $pendingContract->update(['book_id' => $book->id]);
            }
        }

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre "'.$data['titre'].'" ajouté avec succès !');
    }

    public function edit(Book $book)
    {
        $authors = Author::orderBy('nom')->get();
        $book->load('contracts');
        $currentContract = $book->activeContract;
        $unassignedContracts = \App\Models\Contract::where(function ($q) use ($book) {
            $q->whereNull('book_id')->orWhere('book_id', $book->id);
        })->where('author_id', $book->author_id)->get();

        return view('admin.books.edit', compact('book', 'authors', 'currentContract', 'unassignedContracts'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'categorie' => 'required|string|max:100',
            'resume' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:100',
            'disponibilite' => 'nullable|string|max:100',
            'maison_edition' => 'nullable|string|max:100',
            'couleur_couverture' => 'required|string|max:50',
            'prix' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_bestseller' => 'boolean',
            'is_free' => 'nullable|boolean',
            'free_until' => 'nullable|date',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
        ]);

        $data['is_bestseller'] = $request->has('is_bestseller');
        $data['is_free'] = $request->has('is_free');
        $data['free_until'] = $request->filled('free_until') ? $request->input('free_until') : null;

        // Mise à jour de la couverture
        if ($request->hasFile('cover_image')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }

            $file = $request->file('cover_image');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'books/covers/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            // Resize and crop to 400x600
            $image->cover(400, 600);

            // Save as WebP
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['cover_path'] = $path;
        }

        // Mise à jour du PDF
        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_path) {
                Storage::delete($book->pdf_path);
            }
            $data['pdf_path'] = $request->file('pdf_file')->store('books/pdfs');
        }

        $contractId = $request->input('contract_id');

        $book->update($data);

        // Mise à jour de l'assignation du contrat
        if ($contractId) {
            // Détacher les anciens contrats liés à ce livre si différents
            \App\Models\Contract::where('book_id', $book->id)
                ->where('id', '!=', $contractId)
                ->update(['book_id' => null]);

            \App\Models\Contract::where('id', $contractId)->update(['book_id' => $book->id]);
        }

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre "'.$book->titre.'" mis à jour avec succès !');
    }

    public function destroy(Book $book)
    {
        $titre = $book->titre;

        // Nettoyage des fichiers
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }
        if ($book->pdf_path) {
            Storage::delete($book->pdf_path);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre "'.$titre.'" supprimé avec succès !');
    }
}
