<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the vehicles.
     */
    public function index(Request $request)
    {
        $query = Vehiculo::with(['cliente']);

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'LIKE', "%{$search}%")
                    ->orWhere('marca', 'LIKE', "%{$search}%")
                    ->orWhere('modelo', 'LIKE', "%{$search}%")
                    ->orWhere('color', 'LIKE', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por cliente
        if ($request->has('cliente_id') && $request->cliente_id != '') {
            $query->where('cliente_id', $request->cliente_id);
        }

        $vehiculos = $query->latest()->paginate(12);

        // Transformar cada vehículo para agregar la URL de la foto
        $vehiculos->getCollection()->transform(function ($vehiculo) {
            // Construir la URL directamente
            if ($vehiculo->foto) {
                // Verificar si el archivo existe
                if (Storage::disk('public')->exists($vehiculo->foto)) {
                    $vehiculo->foto_url = asset('storage/' . $vehiculo->foto);
                } else {
                    $vehiculo->foto_url = null;
                }
            } else {
                $vehiculo->foto_url = null;
            }

            return $vehiculo;
        });

        $clientes = User::clientes()->activos()->get(['id', 'nombre']);

        return Inertia::render('Admin.Vehiculos.Index', [
            'vehiculos' => $vehiculos,
            'clientes' => $clientes,
            'filters' => $request->only(['search', 'estado', 'cliente_id']),
            'estados' => [
                'activo' => 'Activo',
                'inactivo' => 'Inactivo'
            ]
        ]);
    }
    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $clientes = User::clientes()->activos()->get(['id', 'nombre']);

        return Inertia::render('Admin.Vehiculos.Create', [
            'clientes' => $clientes,
            'marcas_populares' => [
                'Toyota',
                'Honda',
                'Nissan',
                'Ford',
                'Chevrolet',
                'Hyundai',
                'Kia',
                'Volkswagen',
                'Mazda',
                'Mitsubishi',
                'Subaru',
                'BMW',
                'Mercedes-Benz',
                'Audi',
                'Volvo',
                'Renault',
                'Peugeot',
                'Citroën'
            ]
        ]);
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:usuarios,id',
            'placa' => 'required|string|max:20|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'kilometraje' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $data = $request->except('foto');

        // Procesar la foto si se subió
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('vehiculos', 'public');
            $data['foto'] = $fotoPath;
        }

        $vehiculo = Vehiculo::create($data);

        return redirect()->route('admin.vehiculos.index')
            ->with('success', 'Vehículo creado exitosamente.');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load([
            'cliente',
            'citas' => function ($query) {
                $query->with(['diagnostico', 'diagnostico.ordenTrabajo'])->latest();
            }
        ]);

        // Agregar foto_url al vehículo individual
        if ($vehiculo->foto) {
            if (Storage::disk('public')->exists($vehiculo->foto)) {
                $vehiculo->foto_url = asset('storage/' . $vehiculo->foto);
            } else {
                $vehiculo->foto_url = null;
            }
        } else {
            $vehiculo->foto_url = null;
        }

        $estadisticas = $vehiculo->estadisticas;

        return Inertia::render('Admin.Vehiculos.Show', [
            'vehiculo' => $vehiculo,
            'estadisticas' => $estadisticas,
        ]);
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehiculo $vehiculo)
    {
        $clientes = User::clientes()->activos()->get(['id', 'nombre']);

        // Agregar foto_url al vehículo
        if ($vehiculo->foto) {
            if (Storage::disk('public')->exists($vehiculo->foto)) {
                $vehiculo->foto_url = asset('storage/' . $vehiculo->foto);
            } else {
                $vehiculo->foto_url = null;
            }
        } else {
            $vehiculo->foto_url = null;
        }

        return Inertia::render('Admin.Vehiculos.Edit', [
            'vehiculo' => $vehiculo,
            'clientes' => $clientes,
            'marcas_populares' => [
                'Toyota',
                'Honda',
                'Nissan',
                'Ford',
                'Chevrolet',
                'Hyundai',
                'Kia',
                'Volkswagen',
                'Mazda',
                'Mitsubishi',
                'Subaru',
                'BMW',
                'Mercedes-Benz',
                'Audi',
                'Volvo',
                'Renault',
                'Peugeot',
                'Citroën'
            ]
        ]);
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'cliente_id' => 'required|exists:usuarios,id',
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $vehiculo->id,
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'kilometraje' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $data = $request->except('foto');

        // Procesar la nueva foto si se subió
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($vehiculo->tieneFoto()) {
                Storage::disk('public')->delete($vehiculo->foto);
            }

            $fotoPath = $request->file('foto')->store('vehiculos', 'public');
            $data['foto'] = $fotoPath;
        }

        // Si se elimina la foto
        if ($request->has('eliminar_foto') && $request->eliminar_foto) {
            if ($vehiculo->tieneFoto()) {
                Storage::disk('public')->delete($vehiculo->foto);
            }
            $data['foto'] = null;
        }

        $vehiculo->update($data);

        return redirect()->route('admin.vehiculos.index')
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        // Verificar si el vehículo tiene citas activas
        if ($vehiculo->citas()->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso'])->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el vehículo porque tiene citas activas.');
        }

        // Eliminar la foto si existe
        if ($vehiculo->tieneFoto()) {
            Storage::disk('public')->delete($vehiculo->foto);
        }

        $vehiculo->delete();

        return redirect()->route('admin.vehiculos.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }

    /**
     * Update vehicle status
     */
    public function updateStatus(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'estado' => 'required|in:activo,inactivo',
        ]);

        $vehiculo->update([
            'estado' => $request->estado,
        ]);

        return redirect()->back()
            ->with('success', 'Estado del vehículo actualizado exitosamente.');
    }

    /**
     * Remove vehicle photo
     */
    public function removePhoto(Vehiculo $vehiculo)
    {
        if ($vehiculo->tieneFoto()) {
            Storage::disk('public')->delete($vehiculo->foto);
            $vehiculo->update(['foto' => null]);
        }

        return redirect()->back()
            ->with('success', 'Foto del vehículo eliminada exitosamente.');
    }
}
