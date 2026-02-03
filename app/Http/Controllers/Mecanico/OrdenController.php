<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdenController extends Controller
{
    public function index()
    {
        return Inertia::render('Mecanico.Ordenes.Index');
    }

    public function show($id)
    {
        return Inertia::render('Mecanico.Ordenes.Show');
    }

    public function edit($id)
    {
        return Inertia::render('Mecanico.Ordenes.Edit');
    }

    public function update(Request $request, $id)
    {
        // update logic
    }
}
