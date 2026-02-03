<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiagnosticoController extends Controller
{
    public function index()
    {
        return Inertia::render('Mecanico.Diagnosticos.Index');
    }

    public function show($id)
    {
        return Inertia::render('Mecanico.Diagnosticos.Show');
    }

    public function edit($id)
    {
        return Inertia::render('Mecanico.Diagnosticos.Edit');
    }

    public function update(Request $request, $id)
    {
        // update logic
    }
}
