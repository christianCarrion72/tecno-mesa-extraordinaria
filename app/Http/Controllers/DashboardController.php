<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Motor;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Totales
        $totalClientes = Cliente::count();
        $totalMotores = Motor::count();

        // Últimos registros de motores
        $motoresRecientes = Motor::with(['marca', 'modelo'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($motor) {
                return [
                    'numero_serie' => $motor->numero_serie,
                    'marca' => $motor->marca->nombre ?? 'N/A',
                    'modelo' => $motor->modelo->nombre ?? 'N/A',
                    'anio' => $motor->anio,
                ];
            });

        // Últimos registros de clientes
        $clientesRecientes = Cliente::latest()
            ->take(10)
            ->get()
            ->map(function ($cliente) {
                return [
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono,
                ];
            });

        return Inertia::render('Dashboard', [
            'totalClientes' => $totalClientes,
            'totalMotores' => $totalMotores,
            'motoresRecientes' => $motoresRecientes,
            'clientesRecientes' => $clientesRecientes,
        ]);
    }
}
