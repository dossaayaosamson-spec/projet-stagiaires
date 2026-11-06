<?php

namespace App\Http\Controllers;

use App\Services\PublicCatalogueAssistant;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LogicException;
use RuntimeException;
use UnexpectedValueException;

class PublicCatalogueAssistantController extends Controller
{
    public function __invoke(Request $request, PublicCatalogueAssistant $assistant): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        try {
            return response()->json([
                'answer' => $assistant->answer($validated['message']),
            ]);
        } catch (ConnectionException|LogicException|RuntimeException|UnexpectedValueException $exception) {
            Log::warning('Public catalogue assistant request failed.', [
                'failure' => class_basename($exception),
            ]);

            return response()->json([
                'message' => 'L’assistant est momentanément indisponible. Réessayez plus tard.',
            ], 503);
        }
    }
}
