<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request)
    {

        if (!request()->user()->tienePermiso('marca.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar marcas.');
        }

        return inertia('Marcas/Index', [
            'marcas' => Marca::query()
            ->when(
                $request->busqueda,
                function ($query) use ($request) {
                $query->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%']);
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
        if (!request()->user()->tienePermiso('marca.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar marcas.');
        }

        return inertia('Marcas/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('marcas', 'public');
        }

        Marca::create($data);

        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    public function edit(Marca $marca)
    {
        if (!request()->user()->tienePermiso('marca.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar marcas.');
        }
    
        return inertia('Marcas/Edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('marcas', 'public');
        }

        $marca->update($data);

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Marca $marca)
    {
        
        if ($marca->motores()->count() > 0) {
            return redirect()->route('marcas.index')->with('error', 'No se puede eliminar la marca porque tiene motores asociados.');
        }

        $marca->delete();

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada correctamente.');
    }
}
