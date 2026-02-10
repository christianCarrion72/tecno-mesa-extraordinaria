<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageView;
use Illuminate\Support\Facades\Auth;

class TrackPageView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo registrar si el usuario está autenticado
        if (Auth::check()) {
            // Obtener el nombre de la página desde la ruta
            $pageName = $this->getPageName($request);
            
            // No registrar ciertos endpoints (api, etc)
            if (!$this->shouldIgnore($request)) {
                PageView::create([
                    'user_id' => Auth::id(),
                    'page_name' => $pageName,
                    'page_route' => $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referer' => $request->header('referer'),
                ]);
            }
        }

        return $response;
    }

    /**
     * Obtener el nombre de la página basado en la ruta
     */
    private function getPageName(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? '';

        // Mapeo de rutas a nombres de página - SEPARAR index/create/edit/show
        $pageMap = [
            // Dashboard
            'dashboard' => 'Dashboard',
            'admin.dashboard' => 'Dashboard Admin',
            'mecanico.dashboard' => 'Dashboard Mecánico',
            // Admin - Marcas
            'admin.marcas.index' => 'Gestión de Marcas',
            'admin.marcas.create' => 'Crear Marca',
            'admin.marcas.store' => 'Crear Marca',
            'admin.marcas.show' => 'Ver Marca',
            'admin.marcas.edit' => 'Editar Marca',
            'admin.marcas.update' => 'Editar Marca',
            'admin.marcas.destroy' => 'Eliminar Marca',
            // Admin - Modelos
            'admin.modelos.index' => 'Gestión de Modelos',
            'admin.modelos.create' => 'Crear Modelo',
            'admin.modelos.store' => 'Crear Modelo',
            'admin.modelos.show' => 'Ver Modelo',
            'admin.modelos.edit' => 'Editar Modelo',
            'admin.modelos.update' => 'Editar Modelo',
            'admin.modelos.destroy' => 'Eliminar Modelo',
            // Admin - Motores
            'admin.motores.index' => 'Gestión de Motores',
            'admin.motores.create' => 'Crear Motor',
            'admin.motores.store' => 'Crear Motor',
            'admin.motores.show' => 'Ver Motor',
            'admin.motores.edit' => 'Editar Motor',
            'admin.motores.update' => 'Editar Motor',
            'admin.motores.destroy' => 'Eliminar Motor',
            // Admin - Partes
            'admin.partes.index' => 'Gestión de Partes',
            'admin.partes.create' => 'Crear Parte',
            'admin.partes.store' => 'Crear Parte',
            'admin.partes.show' => 'Ver Parte',
            'admin.partes.edit' => 'Editar Parte',
            'admin.partes.update' => 'Editar Parte',
            'admin.partes.destroy' => 'Eliminar Parte',
            
            // Cliente - Vehículos
            'cliente.vehiculos.index' => 'Mis Vehículos',
            'cliente.vehiculos.create' => 'Crear Vehículo',
            'cliente.vehiculos.store' => 'Crear Vehículo',
            'cliente.vehiculos.show' => 'Ver Vehículo',
            'cliente.vehiculos.edit' => 'Editar Vehículo',
            'cliente.vehiculos.update' => 'Editar Vehículo',
            'cliente.vehiculos.destroy' => 'Eliminar Vehículo',
            
            // Cliente - Citas
            'cliente.citas.index' => 'Mis Citas',
            'cliente.citas.create' => 'Crear Cita',
            'cliente.citas.store' => 'Crear Cita',
            'cliente.citas.show' => 'Ver Cita',
            'cliente.citas.edit' => 'Editar Cita',
            'cliente.citas.update' => 'Editar Cita',
            'cliente.citas.destroy' => 'Eliminar Cita',
            
            // Cliente - Órdenes
            'cliente.ordenes.index' => 'Mis Órdenes',
            'cliente.ordenes.show' => 'Ver Orden',
            'cliente.ordenes.aprobar' => 'Aprobar Orden',
            'cliente.ordenes.rechazar' => 'Rechazar Orden',
            
            // Cliente - Pagos
            'cliente.pagos.index' => 'Mis Pagos',
            'cliente.pagos.show' => 'Ver Pago',
            
            // Admin - Clientes
            'admin.clientes.index' => 'Gestión de Clientes',
            'admin.clientes.create' => 'Crear Cliente',
            'admin.clientes.store' => 'Crear Cliente',
            'admin.clientes.show' => 'Ver Cliente',
            'admin.clientes.edit' => 'Editar Cliente',
            'admin.clientes.update' => 'Editar Cliente',
            'admin.clientes.destroy' => 'Eliminar Cliente',
            
            // Admin - Vehículos
            'admin.vehiculos.index' => 'Gestión de Vehículos',
            'admin.vehiculos.create' => 'Crear Vehículo',
            'admin.vehiculos.store' => 'Crear Vehículo',
            'admin.vehiculos.show' => 'Ver Vehículo',
            'admin.vehiculos.edit' => 'Editar Vehículo',
            'admin.vehiculos.update' => 'Editar Vehículo',
            'admin.vehiculos.destroy' => 'Eliminar Vehículo',
            
            // Admin - Servicios
            'admin.servicios.index' => 'Servicios',
            'admin.servicios.create' => 'Crear Servicio',
            'admin.servicios.store' => 'Crear Servicio',
            'admin.servicios.show' => 'Ver Servicio',
            'admin.servicios.edit' => 'Editar Servicio',
            'admin.servicios.update' => 'Editar Servicio',
            'admin.servicios.destroy' => 'Eliminar Servicio',
            
            // Admin - Citas
            'admin.citas.index' => 'Gestión de Citas',
            'admin.citas.create' => 'Crear Cita',
            'admin.citas.store' => 'Crear Cita',
            'admin.citas.show' => 'Ver Cita',
            'admin.citas.edit' => 'Editar Cita',
            'admin.citas.update' => 'Editar Cita',
            'admin.citas.destroy' => 'Eliminar Cita',
            
            // Admin - Diagnósticos
            'admin.diagnosticos.index' => 'Diagnósticos',
            'admin.diagnosticos.create' => 'Crear Diagnóstico',
            'admin.diagnosticos.store' => 'Crear Diagnóstico',
            'admin.diagnosticos.show' => 'Ver Diagnóstico',
            'admin.diagnosticos.edit' => 'Editar Diagnóstico',
            'admin.diagnosticos.update' => 'Editar Diagnóstico',
            'admin.diagnosticos.destroy' => 'Eliminar Diagnóstico',
            
            // Admin - Órdenes de Trabajo
            'admin.ordenes.index' => 'Órdenes de Trabajo',
            'admin.ordenes.create' => 'Crear Orden',
            'admin.ordenes.store' => 'Crear Orden',
            'admin.ordenes.show' => 'Ver Orden',
            'admin.ordenes.edit' => 'Editar Orden',
            'admin.ordenes.update' => 'Editar Orden',
            'admin.ordenes.destroy' => 'Eliminar Orden',
            
            // Admin - Pagos
            'admin.pagos.index' => 'Pagos',
            'admin.pagos.create' => 'Crear Pago',
            'admin.pagos.store' => 'Crear Pago',
            'admin.pagos.show' => 'Ver Pago',
            'admin.pagos.edit' => 'Editar Pago',
            'admin.pagos.update' => 'Editar Pago',
            'admin.pagos.destroy' => 'Eliminar Pago',
            'admin.pagos.cobrar' => 'Cobrar Pago',
            
            // Admin - Usuarios
            'admin.usuarios.index' => 'Gestión de Usuarios',
            'admin.usuarios.create' => 'Crear Usuario',
            'admin.usuarios.store' => 'Crear Usuario',
            'admin.usuarios.edit' => 'Editar Usuario',
            'admin.usuarios.update' => 'Editar Usuario',
            'admin.usuarios.destroy' => 'Eliminar Usuario',
            
            // Admin - Permisos
            'admin.permisos.index' => 'Gestión de Permisos',
            'admin.permisos.actualizar' => 'Actualizar Permisos',
            
            // Admin - Reportes
            'admin.reportes.index' => 'Reportes',
            'admin.reportes.exportar' => 'Exportar Reportes',
            
            // Profile
            'profile.edit' => 'Editar Perfil',
            'profile.update' => 'Actualizar Perfil',
            'profile.destroy' => 'Eliminar Cuenta',
            
            // Mecánico - Órdenes de Trabajo
            'mecanico.ordenes.index' => 'Órdenes de Trabajo',
            'mecanico.ordenes.show' => 'Ver Orden',
            'mecanico.ordenes.edit' => 'Editar Orden',
            'mecanico.ordenes.update' => 'Actualizar Orden',
        ];

        return $pageMap[$routeName] ?? ucfirst(str_replace('-', ' ', $request->path()));
    }

    /**
     * Determinar si se debe ignorar esta ruta
     */
    private function shouldIgnore(Request $request): bool
    {
        $ignoredPaths = [
            'api/',
            'livewire/',
            'telescope/',
            'horizon/',
        ];

        foreach ($ignoredPaths as $path) {
            if (str_starts_with($request->path(), $path)) {
                return true;
            }
        }

        return false;
    }
}
