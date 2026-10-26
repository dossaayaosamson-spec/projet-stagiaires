<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Affiche le formulaire de saisie du code.
     */
    public function show()
    {
        return view('auth.verify-email');
    }

    /**
     * Vérifie le code à 6 chiffres.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|array|size:6',
            'code.*' => 'required|numeric|digits:1',
        ]);

        $inputCode = implode('', $request->code);
        $user = Auth::user();

        if ($user->verification_code === $inputCode) {
            $user->markEmailAsVerified();
            $user->verification_code = null;
            $user->save();

            return redirect()->route('dashboard')->with('message', 'Votre compte a été vérifié avec succès !');
        }

        return back()->withErrors(['verification_code' => 'Le code saisi est incorrect.']);
    }

    /**
     * Renvoie un nouveau code.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->generateVerificationCode();
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Un nouveau code de vérification a été envoyé !');
    }
}
