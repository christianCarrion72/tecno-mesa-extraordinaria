<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\Diagnostico;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Motor;
use App\Models\Parte;
use App\Models\Cliente;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // collect any date filters that might be passed through query string (this
        // mirrors the report page filtering behaviour). if none are provided we
        // simply ignore them and show global data.
        $filtros = [
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $stats = $this->getAdminStats($filtros);

        return Inertia::render('Admin.Dashboard', [
            'stats' => $stats,
        ]);
    }

    private function getAdminStats(array $filtros = [])
    {
        // core KPIs used by the dashboard cards
        $totalClientes = Cliente::count();
        $ordenesEnProceso = OrdenTrabajo::where('estado', 'en_proceso')->count();
        // calcular ingresos del mes usando la tabla 'pagos' ya que no existe
        // 'pago_detalles'. esta tabla contiene el campo 'monto' por cada pago, así
        // que simplemente sumamos ese valor filtrando por mes actual.
        $ingresosMes = \DB::table('pagos')
            ->whereMonth('created_at', now()->month)
            ->sum('monto');

        // últimas órdenes de trabajo (posiblemente filtradas por rango de fechas)
        // obtener órdenes ordenadas por fecha de creación (created_at)
        // cargamos también el cliente para mostrar su nombre en la vista
        $ordenesQuery = OrdenTrabajo::with(['mecanico', 'cliente'])
            ->latest('created_at');
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            // filtramos por created_at ya que el modelo no dispone de fecha_creacion
            $ordenesQuery->whereBetween('created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']]);
        }

        $ordenesRecientes = $ordenesQuery
            ->take(5)
            ->get()
            ->map(function ($orden) {
                return [
                    'id' => $orden->id,
                    // identificador legible para mostrar en UI
                    'identificador' => 'OT-' . $orden->id,
                    'cliente' => $orden->cliente->nombre ?? 'N/A',
                    'mecanico' => $orden->mecanico->nombre ?? 'N/A',
                    'estado' => $orden->estado,
                    'subtotal' => $orden->subtotal,
                    'descripcion' => $orden->descripcion ?? null,
                    // usamos exclusivamente created_at para la fecha de creación
                    'fecha_creacion' => optional($orden->created_at)->format('d/m/Y'),
                ];
            });

        // servicios más solicitados (por cantidad) en órdenes de trabajo
        $serviciosMas = Servicio::select('servicios.nombre')
            ->join('orden_trabajo_servicios', 'servicios.id', '=', 'orden_trabajo_servicios.servicio_id')
            ->selectRaw('servicios.nombre, SUM(orden_trabajo_servicios.cantidad) as total')
            ->groupBy('servicios.nombre')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($row) {
                return [
                    'servicio' => $row->nombre,
                    'cantidad' => $row->total,
                ];
            });

        // inventario (se muestran solo si el usuario tiene permiso en la vista)
        $totalMarcas = Marca::count();
        $totalModelos = Modelo::count();
        $totalMotores = Motor::count();
        $totalPartes = Parte::count();

        // últimas marcas registradas
        $marcasRecientes = Marca::latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($marca) {
                return [
                    'id' => $marca->id,
                    'nombre' => $marca->nombre,
                    'fecha_creacion' => optional($marca->created_at)->format('d/m/Y'),
                ];
            });

        // últimos modelos registrados
        $modelosRecientes = Modelo::latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($modelo) {
                return [
                    'id' => $modelo->id,
                    'nombre' => $modelo->nombre,
                    'fecha_creacion' => optional($modelo->created_at)->format('d/m/Y'),
                ];
            });

        // clientes nuevos (últimos 5)
        $clientesNuevos = Cliente::latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono ?? 'N/A',
                    'fecha_creacion' => optional($cliente->created_at)->format('d/m/Y'),
                ];
            });

        return [
            'total_clientes' => $totalClientes,
            'ordenes_en_proceso' => $ordenesEnProceso,
            'ingresos_mes' => $ingresosMes,
            'ordenes_recientes' => $ordenesRecientes,
            'servicios_mas_solicitados' => $serviciosMas,
            'total_marcas' => $totalMarcas,
            'total_modelos' => $totalModelos,
            'total_motores' => $totalMotores,
            'total_partes' => $totalPartes,
            'marcas_recientes' => $marcasRecientes,
            'modelos_recientes' => $modelosRecientes,
            'clientes_nuevos' => $clientesNuevos,
        ];
    }
}
