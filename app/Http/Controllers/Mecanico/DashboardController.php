<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Diagnostico;
use App\Models\OrdenTrabajo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Motor;
use App\Models\Parte;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'diagnosticos_pendientes' => Diagnostico::where('mecanico_id', $user->id)
                ->where('estado', 'en_revision')
                ->count(),
            'ordenes_en_proceso' => OrdenTrabajo::where('mecanico_id', $user->id)
                ->where('estado', 'en_proceso')
                ->count(),
            'ordenes_completadas_mes' => OrdenTrabajo::where('mecanico_id', $user->id)
                ->where('estado', 'completada')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_marcas' => Marca::count(),
            'total_modelos' => Modelo::count(),
            'total_motores' => Motor::count(),
            'total_partes' => Parte::count(),
        ];

        return Inertia::render('Mecanico.Dashboard', [
            'stats' => $stats,
        ]);
    }
}
