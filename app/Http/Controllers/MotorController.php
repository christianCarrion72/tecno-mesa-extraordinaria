<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;

class MotorController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->user()->tienePermiso('motor.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar motores.');
        }

        return inertia('Motores/Index', [
            'motores' => Motor::with(['marca', 'modelo'])
            ->when(
                $request->busqueda,
                function ($query) use ($request) {
                    $query->whereRaw('LOWER(numero_serie) like LOWER(?)', ['%' . $request->busqueda . '%'])
                          ->orWhereRaw('LOWER(descripcion) like LOWER(?)', ['%' . $request->busqueda . '%'])
                          ->orWhereHas('marca', function($q) use ($request) {
                              $q->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%']);
                          })
                          ->orWhereHas('modelo', function($q) use ($request) {
                              $q->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%']);
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
        if (!request()->user()->tienePermiso('motor.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear motores.');
        }

        $marcas = Marca::orderBy('nombre')->get();
        $modelos = Modelo::orderBy('nombre')->get();

        return inertia('Motores/Create', compact('marcas', 'modelos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_serie' => 'required|string|max:255|unique:motores,numero_serie',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string|max:500',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('motores', 'public');
        }

        Motor::create($data);

        return redirect()->route('motores.index')->with('success', 'Motor creado correctamente.');
    }

    public function edit(Motor $motor)
    {
        if (!request()->user()->tienePermiso('motor.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar motores.');
        }

        $motor->load(['marca', 'modelo']);
        $marcas = Marca::orderBy('nombre')->get();
        $modelos = Modelo::orderBy('nombre')->get();
    
        return inertia('Motores/Edit', compact('motor', 'marcas', 'modelos'));
    }

    public function update(Request $request, Motor $motor)
    {
        $request->validate([
            'numero_serie' => 'required|string|max:255|unique:motores,numero_serie,' . $motor->id,
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string|max:500',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('motores', 'public');
        }

        $motor->update($data);

        return redirect()->route('motores.index')->with('success', 'Motor actualizado correctamente.');
    }

    public function destroy(Motor $motor)
    {
        if ($motor->partes()->count() > 0) {
            return redirect()->route('motores.index')->with('error', 'No se puede eliminar el motor porque tiene partes asociadas.');
        }

        $motor->delete();

        return redirect()->route('motores.index')->with('success', 'Motor eliminado correctamente.');
    }
}
