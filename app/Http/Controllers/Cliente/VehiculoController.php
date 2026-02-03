<?php
// app/Http/Controllers/Cliente/VehiculoController.php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Auth::user()->vehiculos()
            ->withCount([
                'citas' => function ($query) {
                    $query->where('estado', 'completada');
                }
            ])
            ->latest()
            ->get();

        return Inertia::render('Cliente.Vehiculos.Index', [
            'vehiculos' => $vehiculos,
            'estadisticas' => [
                'total' => $vehiculos->count(),
                'activos' => $vehiculos->where('estado', 'activo')->count(),
                'con_citas' => $vehiculos->where('citas_count', '>', 0)->count(),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Cliente.Vehiculos.Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'placa' => 'required|string|max:20|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'kilometraje' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|max:2048',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $vehiculoData = $request->only([
            'placa',
            'marca',
            'modelo',
            'anio',
            'color',
            'kilometraje',
            'observaciones'
        ]);

        $vehiculoData['cliente_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $vehiculoData['foto'] = $request->file('foto')->store('vehiculos', 'public');
        }

        $vehiculo = Vehiculo::create($vehiculoData);

        return redirect()
            ->route('cliente.vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo registrado exitosamente.');
    }

    public function show(Vehiculo $vehiculo)
    {

        $vehiculo->load([
            'citas' => function ($query) {
                $query->latest()->limit(5);
            },
            'citas.diagnostico',
            'citas.diagnostico.ordenTrabajo'
        ]);

        $estadisticas = [
            'total_citas' => $vehiculo->citas()->count(),
            'citas_completadas' => $vehiculo->citas()->where('estado', 'completada')->count(),
            'citas_pendientes' => $vehiculo->citas()->whereIn('estado', ['pendiente', 'confirmada'])->count(),
            'ultimo_servicio' => $vehiculo->citas()
                ->where('estado', 'completada')
                ->latest()
                ->first()?->created_at?->format('d/m/Y'),
        ];

        return Inertia::render('Cliente.Vehiculos.Show', [
            'vehiculo' => $vehiculo,
            'estadisticas' => $estadisticas
        ]);
    }

    public function edit(Vehiculo $vehiculo)
    {
        // Asegurarse de cargar todos los datos necesarios
        $vehiculo->load(['citas']);

        return Inertia::render('Cliente.Vehiculos.Edit', [
            'vehiculo' => $vehiculo
        ]);
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $vehiculo->id,
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'kilometraje' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|max:2048',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $vehiculoData = $request->only([
            'placa',
            'marca',
            'modelo',
            'anio',
            'color',
            'kilometraje',
            'observaciones'
        ]);

        // Solo procesar la foto si se envió una nueva
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($vehiculo->foto) {
                Storage::disk('public')->delete($vehiculo->foto);
            }
            $vehiculoData['foto'] = $request->file('foto')->store('vehiculos', 'public');
        }

        $vehiculo->update($vehiculoData);

        return redirect()
            ->route('cliente.vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {

        // Verificar si tiene citas activas
        if ($vehiculo->citas()->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso'])->exists()) {
            return redirect()
                ->route('cliente.vehiculos.index')
                ->with('error', 'No se puede eliminar el vehículo porque tiene citas activas.');
        }

        // Eliminar foto si existe
        if ($vehiculo->foto) {
            Storage::disk('public')->delete($vehiculo->foto);
        }

        $vehiculo->delete();

        return redirect()
            ->route('cliente.vehiculos.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }

    public function updateKilometraje(Request $request, Vehiculo $vehiculo)
    {

        $request->validate([
            'kilometraje' => 'required|integer|min:' . ($vehiculo->kilometraje + 1)
        ]);

        $vehiculo->update([
            'kilometraje' => $request->kilometraje
        ]);

        return back()->with('success', 'Kilometraje actualizado exitosamente.');
    }
}
