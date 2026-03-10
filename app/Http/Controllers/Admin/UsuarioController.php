<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('rol');

        if ($request->filled('busqueda')) {
            $search = $request->busqueda;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%");
            });
        }

        $usuarios = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Usuarios/Index', [
            'usuarios' => $usuarios,
            'terminosBusqueda' => $request->busqueda,
        ]);
    }

    public function create()
    {
        $roles = Rol::all();

        return Inertia::render('Admin/Usuarios/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'rol_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'rol_id' => $request->rol_id,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('usuarios', 'public');
        }

        User::create($data);

        return redirect()->route('admin.usuarios.index');
    }

    public function edit(User $usuario)
    {
        $roles = Rol::all();

        return Inertia::render('Admin/Usuarios/Edit', [
            'usuario' => $usuario->load('rol'),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'rol_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nombre' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'rol_id' => $request->rol_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('usuarios', 'public');
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index');
    }

    public function destroy(User $usuario)
    {
        try {
            // Verificar si el usuario es el que está logueado
            if (Auth::id() === $usuario->id) {
                return back()->with('error', 'No puede eliminar su propio usuario mientras está activo en el sistema.');
            }

            // Verificar órdenes de trabajo activas asignadas al usuario
            $tieneOrdenesActivas = DB::table('orden_trabajos')
                ->where('usuario_id', $usuario->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($tieneOrdenesActivas) {
                return back()->with('error', 'No se puede eliminar este usuario porque tiene órdenes de trabajo asignadas. Por favor, reasigne las órdenes de trabajo a otro usuario antes de eliminar.');
            }

            // Si tiene órdenes en historial, solo hacer soft delete
            $usuario->delete();
            return back()->with('success', 'Usuario desactivado correctamente. El historial se mantiene disponible.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de restricción de base de datos
            if ($e->getCode() === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return back()->with('error', 'No se puede eliminar este usuario porque tiene registros históricos asociados. El usuario quedará desactivado pero se mantendrá en el historial del sistema.');
            }
            
            return back()->with('error', 'Ocurrió un error al intentar eliminar el usuario. Por favor, intente nuevamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error inesperado. Por favor, contacte al administrador.');
        }
    }
}
