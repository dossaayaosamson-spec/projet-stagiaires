<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class AuthorAIController extends Controller
{
    protected $djangoApiUrl;

    public function __construct()
    {
        $this->djangoApiUrl = env('DJANGO_API_URL', 'https://agaueditions-ia.saeicubetech.com');
    }

    public function createauteur()
    {
        try {
            $response = Http::timeout(30)->get($this->djangoApiUrl.'/api/authors/');

            if ($response->successful()) {
                $data = $response->json();
                // Les données sont dans un tableau, pas des objets
                $authorsArray = $data['authors'] ?? [];

                // Convertir en collection d'objets
                $authors = collect($authorsArray)->map(function ($author) {
                    return (object) $author;
                });

                // Debug: afficher ce qui est reçu
                \Log::info('Auteurs reçus de Django', ['count' => $authors->count(), 'data' => $authorsArray]);
            } else {
                $authors = collect([]);
                \Log::error('Erreur API Django', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            \Log::error('Exception API Django', ['message' => $e->getMessage()]);
            $authors = collect([]);
        }

        return view('manuscripts.auteur', compact('authors'));
    }

    public function show($id)
    {
        try {
            $response = Http::timeout(30)->get($this->djangoApiUrl.'/api/authors/'.$id.'/');

            if (! $response->successful()) {
                abort(404, 'Auteur non trouvé');
            }

            $data = $response->json();

            // Convertir les données en objets
            $author = (object) ($data['author'] ?? []);
            $manuscripts = collect($data['manuscripts'] ?? [])->map(function ($ms) {
                return (object) $ms;
            });

            return view('manuscripts.infoauteur', compact('author', 'manuscripts'));
        } catch (\Exception $e) {
            \Log::error('Erreur récupération auteur', ['id' => $id, 'message' => $e->getMessage()]);
            abort(404, 'Auteur non trouvé');
        }
    }

    // Supprimer un auteur
    public function destroy($id)
    {
        try {
            $response = Http::delete($this->djangoApiUrl.'/api/authors/'.$id.'/');

            if ($response->successful()) {
                // Retourner du JSON pour que fetch() puisse le parser
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
