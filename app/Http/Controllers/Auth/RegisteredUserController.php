<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|lowercase|email|max:100|unique:usuarios,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'tipo' => 'cliente', // Por defecto todos se registran como clientes
            'estado' => 'activo',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirigir al dashboard correspondiente según el tipo de usuario
        return $this->redirectToDashboard($user);
    }

    /**
     * Redirige al usuario a su dashboard correspondiente según su tipo
     */
    private function redirectToDashboard(User $user): RedirectResponse
    {
        return match($user->tipo) {
            'propietario', 'secretaria' => redirect()->route('admin.dashboard'),
            'mecanico' => redirect()->route('mecanico.dashboard'),
            default => redirect()->route('cliente.dashboard'), // cliente
        };
    }
}
