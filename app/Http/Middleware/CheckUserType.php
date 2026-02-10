<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$types): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->rol) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        $roleName = strtolower($user->rol->nombre);
        $allowedTypes = array_map('strtolower', $types);

        if (! in_array($roleName, $allowedTypes, true)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
