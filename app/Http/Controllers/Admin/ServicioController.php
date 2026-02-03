<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServicioController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request)
    {
        $query = Servicio::query();

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%")
                    ->orWhere('tipo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $servicios = $query->latest()->paginate(12);

        return Inertia::render('Admin.Servicios.Index', [
            'servicios' => $servicios,
            'filters' => $request->only(['search', 'tipo', 'estado']),
            'tipos' => [
                'diagnostico' => 'Diagnóstico',
                'mantenimiento' => 'Mantenimiento',
                'reparacion' => 'Reparación'
            ],
            'estados' => [
                'activo' => 'Activo',
                'inactivo' => 'Inactivo'
            ]
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return Inertia::render('Admin.Servicios.Create', [
            'tipos' => [
                'diagnostico' => 'Diagnóstico',
                'mantenimiento' => 'Mantenimiento',
                'reparacion' => 'Reparación'
            ]
        ]);
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:diagnostico,mantenimiento,reparacion',
            'precio_base' => 'required|numeric|min:0',
            'duracion_estimada' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Servicio::create($request->all());

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio creado exitosamente.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Servicio $servicio)
    {
        return Inertia::render('Admin.Servicios.Edit', [
            'servicio' => $servicio,
            'tipos' => [
                'diagnostico' => 'Diagnóstico',
                'mantenimiento' => 'Mantenimiento',
                'reparacion' => 'Reparación'
            ]
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:diagnostico,mantenimiento,reparacion',
            'precio_base' => 'required|numeric|min:0',
            'duracion_estimada' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $servicio->update($request->all());

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio actualizado exitosamente.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Servicio $servicio)
    {
        // Verificar si el servicio está siendo usado en órdenes de trabajo
        if ($servicio->ordenServicios()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el servicio porque está siendo usado en órdenes de trabajo.');
        }

        $servicio->delete();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio eliminado exitosamente.');
    }

    /**
     * Update service status
     */
    public function updateStatus(Request $request, Servicio $servicio)
    {
        $request->validate([
            'estado' => 'required|in:activo,inactivo',
        ]);

        $servicio->update([
            'estado' => $request->estado,
        ]);

        return redirect()->back()
            ->with('success', 'Estado del servicio actualizado exitosamente.');
    }
}
