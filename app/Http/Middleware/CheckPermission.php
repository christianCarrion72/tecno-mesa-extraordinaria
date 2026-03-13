<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->rol) {
            abort(403, 'No tiene permiso para acceder a esta página.');
        }

        $roleName = strtolower($user->rol->nombre ?? '');

        // El propietario siempre puede entrar.
        if ($roleName === 'propietario') {
            return $next($request);
        }

        foreach ($permisos as $permiso) {
            if ($user->tienePermiso($permiso)) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', $this->buildDeniedMessage($permisos));
    }

    private function buildDeniedMessage(array $permisos): string
    {
        $firstPermission = $permisos[0] ?? '';
        $permissionParts = explode('.', $firstPermission);
        $action = strtolower(end($permissionParts) ?: 'acceder');

        $actionLabel = match ($action) {
            'crear' => 'CREAR',
            'editar' => 'EDITAR',
            'eliminar' => 'ELIMINAR',
            'actualizar' => 'ACTUALIZAR',
            'listar' => 'VER',
            default => 'ACCEDER',
        };

        return "NO TIENE PERMISO PARA {$actionLabel}.";
    }
}
