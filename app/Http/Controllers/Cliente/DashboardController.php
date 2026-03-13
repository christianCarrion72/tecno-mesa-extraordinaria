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

        $flashData = array_filter([
            'error'   => session('error'),
            'success' => session('success'),
        ]);

        if (in_array($roleName, ['Propietario', 'Secretaria'])) {
            return redirect()->route('admin.dashboard')->with($flashData);
        }

        if ($roleName === 'Mecanico') {
            return redirect()->route('mecanico.dashboard')->with($flashData);
        }

        $data = [];

        return Inertia::render('Cliente.Dashboard', [
            'userType' => $roleName,
            'stats' => $data,
        ]);
    }
}
