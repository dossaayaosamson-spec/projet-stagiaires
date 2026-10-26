<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::latest('date_evenement')->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('lien_inscription')) {
            $encoded = $request->lien_inscription;
            // Reconvertit depuis base64 URL-safe
            $encoded = str_replace(['-', '_'], ['+', '/'], $encoded);
            $padded = str_pad($encoded, strlen($encoded) + (4 - strlen($encoded) % 4) % 4, '=');
            $request->merge([
                'lien_inscription' => base64_decode($padded),
            ]);
        }
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_evenement' => 'required|date',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'is_ecla' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_ecla'] = $request->has('is_ecla');
        $data['lien_inscription'] = $request->input('lien_inscription');

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'events/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            // Resize and crop to 800x450 (16:9 ratio common for event banners)
            $image->cover(800, 450);

            // Save as WebP
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['image_path'] = $path;
        }

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement créé avec succès !');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        if ($request->filled('lien_inscription')) {
            $encoded = $request->lien_inscription;
            // Reconvertit depuis base64 URL-safe
            $encoded = str_replace(['-', '_'], ['+', '/'], $encoded);
            $padded = str_pad($encoded, strlen($encoded) + (4 - strlen($encoded) % 4) % 4, '=');
            $request->merge([
                'lien_inscription' => base64_decode($padded),
            ]);
        }
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_evenement' => 'required|date',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'is_ecla' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_ecla'] = $request->has('is_ecla');
        $data['lien_inscription'] = $request->input('lien_inscription');

        if ($request->hasFile('image_file')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }

            $file = $request->file('image_file');
            $filename = time().'_'.uniqid().'.webp';
            $path = 'events/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->decode($file);

            // Resize and crop to 800x450
            $image->cover(800, 450);

            // Save as WebP
            $image->save(storage_path('app/public/'.$path), quality: 80);

            $data['image_path'] = $path;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement mis à jour !');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement supprimé.');
    }
}
