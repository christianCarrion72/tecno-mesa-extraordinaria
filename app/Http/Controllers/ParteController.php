<?php

namespace App\Http\Controllers;

use App\Models\Parte;
use App\Models\Motor;
use Illuminate\Http\Request;

class ParteController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->user()->tienePermiso('parte.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar partes.');
        }

        return inertia('Partes/Index', [
            'partes' => Parte::with('motor')
            ->when(
                $request->busqueda,
                function ($query) use ($request) {
                    $query->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%'])
                          ->orWhereHas('motor', function($q) use ($request) {
                              $q->whereRaw('LOWER(numero_serie) like LOWER(?)', ['%' . $request->busqueda . '%']);
                          });
                }
            )
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString(),

            'terminosBusqueda' => $request->busqueda
        ]);
    }

    public function create()
    {
        if (!request()->user()->tienePermiso('parte.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear partes.');
        }

        $motores = Motor::with(['marca', 'modelo'])->orderBy('numero_serie')->get();

        return inertia('Partes/Create', compact('motores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'motor_id' => 'required|exists:motores,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('partes', 'public');
        }

        Parte::create($data);

        return redirect()->route('partes.index')->with('success', 'Parte creada correctamente.');
    }

    public function edit(Parte $parte)
    {
        if (!request()->user()->tienePermiso('parte.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar partes.');
        }

        $parte->load('motor');
        $motores = Motor::with(['marca', 'modelo'])->orderBy('numero_serie')->get();
    
        return inertia('Partes/Edit', compact('parte', 'motores'));
    }

    public function update(Request $request, Parte $parte)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'motor_id' => 'required|exists:motores,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('partes', 'public');
        }

        $parte->update($data);

        return redirect()->route('partes.index')->with('success', 'Parte actualizada correctamente.');
    }

    public function destroy(Parte $parte)
    {
        $parte->delete();

        return redirect()->route('partes.index')->with('success', 'Parte eliminada correctamente.');
    }
}
