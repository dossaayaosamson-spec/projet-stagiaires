<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom_famille' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:client,admin,auteur',
            'alias' => 'nullable|string|max:255|unique:users,alias,'.$user->id,
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "'.$user->nom_complet.'" mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $user->nom_complet;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "'.$nom.'" supprimé.');
    }
}
