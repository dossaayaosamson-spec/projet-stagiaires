<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slides = Slide::orderBy('order')->get();

        return view('admin.slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.slides.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link_url' => 'nullable|url|max:255',
            'order' => 'integer|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'slides/'.$filename;

            Storage::disk('public')->makeDirectory('slides');

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['image_path'] = $path;
        }

        Slide::create($data);

        return redirect()->route('admin.slides.index')->with('success', 'Slide ajouté avec succès !');
    }

    public function edit(Slide $slide)
    {
        return view('admin.slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide)
    {
        $data = $request->validate([
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link_url' => 'nullable|url|max:255',
            'order' => 'integer|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image_file')) {
            if ($slide->image_path) {
                Storage::disk('public')->delete($slide->image_path);
            }

            $file = $request->file('image_file');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'slides/'.$filename;

            Storage::disk('public')->makeDirectory('slides');

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['image_path'] = $path;
        }

        $slide->update($data);

        return redirect()->route('admin.slides.index')->with('success', 'Slide mis à jour avec succès !');
    }

    public function destroy(Slide $slide)
    {
        if ($slide->image_path) {
            Storage::disk('public')->delete($slide->image_path);
        }
        $slide->delete();

        return redirect()->route('admin.slides.index')->with('success', 'Slide supprimé avec succès !');
    }
}
