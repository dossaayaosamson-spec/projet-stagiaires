<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::latest()->paginate(15);

        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'initiales' => 'nullable|string|max:10',
            'bio' => 'nullable|string',
            'couleur_avatar' => 'required|string|max:50',
            'nb_livres' => 'integer|min:0',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if (empty($data['initiales'])) {
            $words = explode(' ', $data['nom']);
            $initials = '';
            foreach ($words as $word) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
            $data['initiales'] = substr($initials, 0, 4);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'avatars/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            // Resize and crop to 200x200 (optimized for small avatars)
            $image->cover(200, 200);

            // Save as WebP with 80% quality
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['avatar'] = $path;
        }

        Author::create($data);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur "'.$data['nom'].'" ajouté avec succès !');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'initiales' => 'nullable|string|max:10',
            'bio' => 'nullable|string',
            'couleur_avatar' => 'required|string|max:50',
            'nb_livres' => 'integer|min:0',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if (empty($data['initiales'])) {
            $words = explode(' ', $data['nom']);
            $initials = '';
            foreach ($words as $word) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
            $data['initiales'] = substr($initials, 0, 4);
        }

        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar si nécessaire
            if ($author->avatar && Storage::disk('public')->exists($author->avatar)) {
                Storage::disk('public')->delete($author->avatar);
            }

            $file = $request->file('avatar');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'avatars/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            // Resize and crop to 200x200
            $image->cover(200, 200);

            // Save as WebP with 80% quality
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['avatar'] = $path;
        }

        $author->update($data);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur "'.$author->nom.'" mis à jour avec succès !');
    }

    public function destroy(Author $author)
    {
        $nom = $author->nom;
        $author->delete();

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur "'.$nom.'" supprimé avec succès !');
    }
}
