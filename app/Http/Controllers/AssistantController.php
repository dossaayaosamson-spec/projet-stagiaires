<?php

namespace App\Http\Controllers;

use App\Services\AssistantKnowledge;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssistantController extends Controller
{
    public function chat(Request $request, AssistantKnowledge $knowledge): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'historique' => ['sometimes', 'array', 'max:6'],
            'historique.*.role' => ['required', 'string', 'in:user,assistant'],
            'historique.*.content' => ['required', 'string', 'max:500'],
        ]);
        try {
            $response = Http::timeout(30)
                ->post(rtrim(config('services.agent.url'), '/').'/chat', [
                    'message' => $validated['message'],
                    'connaissance' => $knowledge->forQuestion($validated['message']),
                    'historique' => $validated['historique'] ?? [],
                ]);
        } catch (ConnectionException) {
            return $this->unavailableResponse();
        }

        $answer = $response->json('reponse');

        if (! $response->successful() || ! is_string($answer) || trim($answer) === '') {
            return $this->unavailableResponse();
        }

        return response()->json(['reponse' => trim($answer)]);
    }

    private function unavailableResponse(): JsonResponse
    {
        return response()->json([
            'message' => "L'assistant est momentanément indisponible, réessayez plus tard.",
        ], 503);
    }
}
