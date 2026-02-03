<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirigir según el tipo de usuario
        $user = Auth::user();
        return $this->redirectToDashboard($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirige al usuario a su dashboard correspondiente según su tipo
     */
    private function redirectToDashboard($user): RedirectResponse
    {
        return match($user->tipo) {
            'propietario', 'secretaria' => redirect()->intended(route('admin.dashboard', absolute: false)),
            'mecanico' => redirect()->intended(route('mecanico.dashboard', absolute: false)),
            default => redirect()->intended(route('cliente.dashboard', absolute: false)), // cliente
        };
    }
}
