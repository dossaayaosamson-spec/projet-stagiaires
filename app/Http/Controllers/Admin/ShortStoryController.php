<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\ShortStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ShortStoryController extends Controller
{
    public function index()
    {
        $stories = ShortStory::with('author')->latest()->paginate(15);

        return view('admin.stories.index', compact('stories'));
    }

    public function create()
    {
        $authors = Author::orderBy('nom')->get();

        return view('admin.stories.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'extrait' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'tags' => 'nullable|string',
            'gradient' => 'required|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['is_free'] = $request->has('is_free');

        $author = Author::findOrFail($data['author_id']);
        $data['auteur'] = $author->nom;

        $data['tags'] = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'stories/covers/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            $image->cover(400, 600);

            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['cover_path'] = $path;
        }

        ShortStory::create($data);

        return redirect()->route('admin.stories.index')
            ->with('success', 'Histoire "'.$data['titre'].'" ajoutée avec succès !');
    }

    public function edit(ShortStory $story)
    {
        $authors = Author::orderBy('nom')->get();

        return view('admin.stories.edit', compact('story', 'authors'));
    }

    public function update(Request $request, ShortStory $story)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'extrait' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'tags' => 'nullable|string',
            'gradient' => 'required|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['is_free'] = $request->has('is_free');

        $author = Author::findOrFail($data['author_id']);
        $data['auteur'] = $author->nom;

        $data['tags'] = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));

        if ($request->hasFile('cover_image')) {
            if ($story->cover_path) {
                Storage::disk('public')->delete($story->cover_path);
            }

            $file = $request->file('cover_image');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'stories/covers/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            $image->cover(400, 600);

            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['cover_path'] = $path;
        }

        $story->update($data);

        return redirect()->route('admin.stories.index')
            ->with('success', 'Histoire "'.$story->titre.'" mise à jour avec succès !');
    }

    public function destroy(ShortStory $story)
    {
        $titre = $story->titre;

        if ($story->cover_path) {
            Storage::disk('public')->delete($story->cover_path);
        }

        $story->delete();

        return redirect()->route('admin.stories.index')
            ->with('success', 'Histoire "'.$titre.'" supprimée avec succès !');
    }
}
