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

        $data = [];

        if ($user->esCliente()) {
            $data = [
                'citas_pendientes' => $user->citas()->where('estado', 'pendiente')->count(),
                'vehiculos' => $user->vehiculos()->activos()->count(),
                'ultimas_citas' => $user->citas()->with('vehiculo')->latest()->take(5)->get(),
            ];
        } elseif ($user->esMecanico()) {
            $data = [
                'diagnosticos_pendientes' => $user->diagnosticos()->where('estado', 'en_revision')->count(),
                'ordenes_proceso' => $user->ordenesTrabajo()->where('estado', 'en_proceso')->count(),
            ];
        }

        return Inertia::render('Cliente.Dashboard', [
            'userType' => $user->tipo,
            'stats' => $data,
        ]);
    }
}
