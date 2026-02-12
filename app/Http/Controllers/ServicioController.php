<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServicioController extends Controller
{
    // LISTAR SERVICIOS
    public function index()
    {
        if (!request()->user()->tienePermiso('servicio.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar servicios.');
        }

        return inertia('Servicios/Index', [
            'servicios' => Servicio::orderBy('id', 'desc')->paginate(10)
        ]);
    }

    // FORM CREAR
    public function create()
    {
        if (!request()->user()->tienePermiso('servicio.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear servicios.');
        }

        return inertia('Servicios/Create');
    }

    // GUARDAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'costo'       => 'required|numeric|min:0',
        ]);

        Servicio::create($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    // FORM EDITAR
    public function edit(Servicio $servicio)
    {
        if (!request()->user()->tienePermiso('servicio.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar servicios.');
        }

        return inertia('Servicios/Edit', [
            'servicio' => $servicio
        ]);
    }

    // ACTUALIZAR
    public function update(Request $request, Servicio $servicio)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'costo'       => 'required|numeric|min:0',
        ]);

        $servicio->update($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    // BORRADO LÓGICO
    public function destroy(Servicio $servicio)
    {
        if (!request()->user()->tienePermiso('servicio.eliminar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para eliminar servicios.');
        }

        $servicio->delete();

        return back()->with('success', 'Servicio eliminado.');
    }
}
