<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidenciaController extends Controller
{
    public function index()
    {
        if (!request()->user()->tienePermiso('incidencia.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar incidencias.');
        }

        return inertia('Incidencias/Index', [
            'incidencias' => Incidencia::with('ordenTrabajo')
                ->orderBy('id', 'desc')
                ->paginate(10)
        ]);
    }

    public function create(OrdenTrabajo $ordenTrabajo)
    {
        if (!request()->user()->tienePermiso('incidencia.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear incidencias.');
        }

        return inertia('Incidencias/Create', [
            'orden' => $ordenTrabajo
        ]);
    }

    public function store(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        if (!request()->user()->tienePermiso('incidencia.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear incidencias.');
        }

        $data = $request->validate([
            'descripcion' => 'required|string|max:500',
            'fecha'       => 'nullable|date',
            // enum in DB: 'registrada', 'en revisión', 'resuelta'
            'estado'      => 'required|in:registrada,en revisión,resuelta',
        ]);

        // Si la incidencia se marca como resuelta, poner fecha automática si está vacía
        if ($data['estado'] === 'resuelta' && empty($data['fecha'])) {
            $data['fecha'] = now()->toDateString();
        }

        $data['orden_trabajo_id'] = $ordenTrabajo->id;

        Incidencia::create($data);

        // Después de crear → volver al Show de la orden
        return redirect()->route('orden-trabajos.show', $ordenTrabajo->id)
            ->with('success', 'Incidencia registrada correctamente.');
    }

    public function edit(Incidencia $incidencia)
    {
        if (!request()->user()->tienePermiso('incidencia.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar incidencias.');
        }

        return inertia('Incidencias/Edit', [
            'incidencia' => $incidencia,
            'orden'      => $incidencia->ordenTrabajo,
        ]);
    }

    public function update(Request $request, Incidencia $incidencia)
    {
        if (!request()->user()->tienePermiso('incidencia.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar incidencias.');
        }

        $data = $request->validate([
            'descripcion' => 'required|string|max:500',
            'fecha'       => 'nullable|date',
            // enum in DB: 'registrada', 'en revisión', 'resuelta'
            'estado'      => 'required|in:registrada,en revisión,resuelta',
        ]);

        // Si se marca como resuelta → asignar fecha automática si vacía
        if ($data['estado'] === 'resuelta' && empty($data['fecha'])) {
            $data['fecha'] = now()->toDateString();
        }

        $incidencia->update($data);

        // Volver al Show de la orden
        return redirect()->route('orden-trabajos.show', $incidencia->orden_trabajo_id)
            ->with('success', 'Incidencia actualizada correctamente.');
    }

    public function destroy(Incidencia $incidencia)
    {
        if (!request()->user()->tienePermiso('incidencia.eliminar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para eliminar incidencias.');
        }

        $ordenId = $incidencia->orden_trabajo_id;

        $incidencia->delete();

        return redirect()->route('orden-trabajos.show', $ordenId)
            ->with('success', 'Incidencia eliminada correctamente.');
    }
}
