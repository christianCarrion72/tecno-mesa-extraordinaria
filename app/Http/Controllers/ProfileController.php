<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => false, // No tenemos verificación de email en tu BD
            'status' => session('status'),
            'user' => $request->user()->only(['id', 'nombre', 'email', 'telefono', 'direccion', 'foto', 'tipo']),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Actualizar los campos según tu base de datos
        $user = $request->user();
        $user->nombre = $request->nombre;
        $user->email = $request->email;
        $user->telefono = $request->telefono;
        $user->direccion = $request->direccion;

        // Si se cambió el email, podrías resetear la verificación si la tuvieras
        // if ($user->isDirty('email')) {
        //     $user->email_verified_at = null;
        // }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Perfil actualizado correctamente.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
