<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('welcome');
        }

        $roleName = $user->rol->nombre ?? null;

        if (in_array($roleName, ['Propietario', 'Secretaria'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($roleName === 'Mecanico') {
            return redirect()->route('mecanico.dashboard');
        }

        $data = [];

        return Inertia::render('Cliente.Dashboard', [
            'userType' => $roleName,
            'stats' => $data,
        ]);
    }
}
