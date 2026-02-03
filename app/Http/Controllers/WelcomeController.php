<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Servicio;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $servicios = Servicio::activos()
            ->select('id', 'nombre', 'descripcion', 'tipo', 'precio_base')
            ->get()
            ->groupBy('tipo');

        return Inertia::render('Welcome', [
            'canLogin' => route('login'),
            'canRegister' => route('register'),
            'servicios' => $servicios,
        ]);
    }
}