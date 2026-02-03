<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClienteController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        // Cambiar: usar User::query() en lugar de User::clientes() para ver todos los usuarios
        $query = User::query()->withCount('vehiculos');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo (nuevo)
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo', $request->tipo);
        }

        $clientes = $query->latest()->paginate(10);

        return Inertia::render('Admin.Clientes.Index', [
            'clientes' => $clientes,
            'filters' => $request->only(['search', 'estado', 'tipo']),
            'estados' => [
                'activo' => 'Activo',
                'inactivo' => 'Inactivo'
            ],
            'tipos' => [
                'cliente' => 'Cliente',
                'mecanico' => 'Mecánico',
                'secretaria' => 'Secretaria',
                'propietario' => 'Propietario'
            ]
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return Inertia::render('Admin.Clientes.Create', [
            'tipos' => [
                'cliente' => 'Cliente',
                'mecanico' => 'Mecánico',
                'secretaria' => 'Secretaria',
                'propietario' => 'Propietario'
            ]
        ]);
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'tipo' => 'required|in:cliente,mecanico,secretaria,propietario',
            'password' => 'required|string|min:8|confirmed',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $cliente = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'tipo' => $request->tipo,
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified client.
     */
    public function show(User $cliente)
    {
        // Cambiar: ya no verificamos que sea cliente, puede ser cualquier tipo de usuario
        $cliente->load([
            'vehiculos' => function ($query) {
                $query->latest();
            },
            'citas' => function ($query) {
                $query->with('vehiculo')->latest()->take(5);
            }
        ]);

        $estadisticas = [
            'total_vehiculos' => $cliente->vehiculos()->count(),
            'total_citas' => $cliente->citas()->count(),
            'citas_pendientes' => $cliente->citas()->where('estado', 'pendiente')->count(),
            'ultima_cita' => $cliente->citas()->latest()->first()?->created_at->format('d/m/Y'),
        ];

        return Inertia::render('Admin.Clientes.Show', [
            'cliente' => $cliente,
            'estadisticas' => $estadisticas,
            'tipos' => [
                'cliente' => 'Cliente',
                'mecanico' => 'Mecánico',
                'secretaria' => 'Secretaria',
                'propietario' => 'Propietario'
            ]
        ]);
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(User $cliente)
    {
        // Cambiar: ya no verificamos que sea cliente
        return Inertia::render('Admin/Clientes/Edit', [
            'cliente' => $cliente,
            'tipos' => [
                'cliente' => 'Cliente',
                'mecanico' => 'Mecánico',
                'secretaria' => 'Secretaria',
                'propietario' => 'Propietario'
            ]
        ]);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, User $cliente)
    {
        // Cambiar: ya no verificamos que sea cliente
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuarios,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'tipo' => 'required|in:cliente,mecanico,secretaria,propietario',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $cliente->update([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'tipo' => $request->tipo,
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(User $cliente)
    {
        // Cambiar: ya no verificamos que sea cliente
        // Verificar si el usuario tiene vehículos o citas activas
        if ($cliente->vehiculos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el usuario porque tiene vehículos registrados.');
        }

        if ($cliente->citas()->whereIn('estado', ['pendiente', 'confirmada'])->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el usuario porque tiene citas activas.');
        }

        $cliente->delete();

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Update client status
     */
    public function updateStatus(Request $request, User $cliente)
    {
        // Cambiar: ya no verificamos que sea cliente
        $request->validate([
            'estado' => 'required|in:activo,inactivo',
        ]);

        $cliente->update([
            'estado' => $request->estado,
        ]);

        return redirect()->back()
            ->with('success', 'Estado del usuario actualizado exitosamente.');
    }
}
