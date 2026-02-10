<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermisoController extends Controller
{
    public function index()
    {
        $roles = Rol::with('permisos:id')->get(['id', 'nombre', 'descripcion']);
        $permisos = Permiso::select('id', 'nombre', 'descripcion')->get();

        $rolesFormatted = $roles->map(function ($rol) {
            return [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
                'descripcion' => $rol->descripcion,
                'permisos' => $rol->permisos->pluck('id')->toArray(),
            ];
        });

        return Inertia::render('Admin/Permisos/Index', [
            'roles' => $rolesFormatted,
            'permisos' => $permisos,
        ]);
    }

    public function actualizar(Request $request)
    {
        $data = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'permisos' => 'array',
            'permisos.*' => 'integer|exists:permisos,id',
        ]);

        $rol = Rol::findOrFail($data['rol_id']);
        $rol->permisos()->sync($data['permisos'] ?? []);

        return redirect()->back();
    }
}

