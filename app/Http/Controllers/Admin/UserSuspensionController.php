<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSuspension;
use App\Services\ModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserSuspensionController extends Controller
{
    public function __construct(private ModerationService $moderationService) {}

    /**
     * Suspend un utilisateur.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'motif' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        if ($user->role === 'admin') {
            return back()->withErrors([
                'motif' => 'Impossible de suspendre un administrateur.',
            ]);
        }

        $this->moderationService->suspendreUtilisateur(auth()->user(), $user, $validated['motif']);

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->nom_complet} a été suspendu.");
    }

    /**
     * Lève une suspension existante.
     */
    public function destroy(UserSuspension $suspension): RedirectResponse
    {
        $this->moderationService->leverSuspension(auth()->user(), $suspension);

        return redirect()->route('admin.users.index')
            ->with('success', 'La suspension a été levée.');
    }
}
