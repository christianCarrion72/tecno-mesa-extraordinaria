<?php
// app/Http/Controllers/Admin/ReporteController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtros = [
            'fecha_inicio' => $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d')),
            'fecha_fin' => $request->get('fecha_fin', Carbon::now()->format('Y-m-d')),
            'tipo_reporte' => $request->get('tipo_reporte', 'financiero'),
            'periodo' => $request->get('periodo', 'mensual')
        ];

        // Obtener datos según el tipo de reporte
        switch ($filtros['tipo_reporte']) {
            case 'financiero':
                $datos = $this->getReporteFinanciero($filtros);
                break;
            case 'servicios':
                $datos = $this->getReporteServicios($filtros);
                break;
            case 'mecanicos':
                $datos = $this->getReporteMecanicos($filtros);
                break;
            case 'clientes':
                $datos = $this->getReporteClientes($filtros);
                break;
            case 'vehiculos':
                $datos = $this->getReporteVehiculos($filtros);
                break;
            default:
                $datos = $this->getReporteFinanciero($filtros);
        }

        // Guardar en cache si se solicita
        if ($request->has('guardar_cache')) {
            $this->guardarEnCache($filtros, $datos);
        }

        return inertia('Admin/Reportes/Index', [
            'datos' => $datos,
            'filtros' => $filtros,
            'estadisticasGenerales' => $this->getEstadisticasGenerales()
        ]);
    }

    private function getReporteFinanciero($filtros)
    {
        $ingresosPorDia = DB::table('pago_detalles')
            ->whereBetween('fecha_pago', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                DB::raw('DATE(fecha_pago) as fecha'),
                DB::raw('SUM(monto) as total'),
                DB::raw('COUNT(*) as cantidad_pagos'),
                'metodo_pago'
            )
            ->groupBy('fecha', 'metodo_pago')
            ->orderBy('fecha')
            ->get();

        $ingresosPorMetodo = DB::table('pago_detalles')
            ->whereBetween('fecha_pago', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                'metodo_pago',
                DB::raw('SUM(monto) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('metodo_pago')
            ->get();

        $estadoPagos = DB::table('pagos')
            ->select(
                'estado',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(monto_total) as monto_total'),
                DB::raw('SUM(monto_pagado) as monto_pagado')
            )
            ->groupBy('estado')
            ->get();

        return [
            'ingresos_por_dia' => $ingresosPorDia,
            'ingresos_por_metodo' => $ingresosPorMetodo,
            'estado_pagos' => $estadoPagos,
            'resumen' => [
                'ingresos_totales' => $ingresosPorDia->sum('total'),
                'pagos_totales' => $ingresosPorDia->sum('cantidad_pagos'),
                'promedio_pago' => $ingresosPorDia->avg('total')
            ]
        ];
    }

    private function getReporteServicios($filtros)
    {
        $serviciosMasSolicitados = DB::table('orden_servicios as os')
            ->join('servicios as s', 'os.servicio_id', '=', 's.id')
            ->join('ordenes_trabajo as ot', 'os.orden_trabajo_id', '=', 'ot.id')
            ->whereBetween('ot.fecha_creacion', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                's.nombre',
                's.tipo',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(os.subtotal) as ingresos'),
                DB::raw('AVG(os.precio_unitario) as precio_promedio')
            )
            ->groupBy('s.id', 's.nombre', 's.tipo')
            ->orderByDesc('cantidad')
            ->get();

        $ingresosPorTipoServicio = DB::table('orden_servicios as os')
            ->join('servicios as s', 'os.servicio_id', '=', 's.id')
            ->join('ordenes_trabajo as ot', 'os.orden_trabajo_id', '=', 'ot.id')
            ->whereBetween('ot.fecha_creacion', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                's.tipo',
                DB::raw('SUM(os.subtotal) as ingresos'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('s.tipo')
            ->get();

        return [
            'servicios_mas_solicitados' => $serviciosMasSolicitados,
            'ingresos_por_tipo' => $ingresosPorTipoServicio,
            'resumen' => [
                'total_servicios' => $serviciosMasSolicitados->sum('cantidad'),
                'ingresos_totales' => $serviciosMasSolicitados->sum('ingresos'),
                'servicio_mas_popular' => $serviciosMasSolicitados->first()
            ]
        ];
    }

    private function getReporteMecanicos($filtros)
    {
        $rendimientoMecanicos = DB::table('ordenes_trabajo as ot')
            ->join('usuarios as u', 'ot.mecanico_id', '=', 'u.id')
            ->whereBetween('ot.fecha_creacion', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                'u.id',
                'u.nombre',
                DB::raw('COUNT(*) as total_ordenes'),
                DB::raw('SUM(ot.subtotal) as ingresos_generados'),
                DB::raw('AVG(ot.subtotal) as promedio_por_orden'),
                DB::raw("COUNT(CASE WHEN ot.estado = 'completada' THEN 1 END) as ordenes_completadas"),
                DB::raw("COUNT(CASE WHEN ot.estado = 'en_proceso' THEN 1 END) as ordenes_en_proceso")
            )
            ->groupBy('u.id', 'u.nombre')
            ->orderByDesc('ingresos_generados')
            ->get();

        $diagnosticosPorMecanico = DB::table('diagnosticos as d')
            ->join('usuarios as u', 'd.mecanico_id', '=', 'u.id')
            ->whereBetween('d.fecha_diagnostico', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                'u.nombre',
                DB::raw('COUNT(*) as total_diagnosticos'),
                DB::raw("COUNT(CASE WHEN d.estado = 'completado' THEN 1 END) as diagnosticos_completados")
            )
            ->groupBy('u.id', 'u.nombre')
            ->get();

        return [
            'rendimiento_mecanicos' => $rendimientoMecanicos,
            'diagnosticos_por_mecanico' => $diagnosticosPorMecanico,
            'resumen' => [
                'mecanico_top' => $rendimientoMecanicos->first(),
                'total_ingresos_mecanicos' => $rendimientoMecanicos->sum('ingresos_generados')
            ]
        ];
    }

    private function getReporteClientes($filtros)
    {
        $clientesTop = DB::table('pagos as p')
            ->join('ordenes_trabajo as ot', 'p.orden_trabajo_id', '=', 'ot.id')
            ->join('diagnosticos as d', 'ot.diagnostico_id', '=', 'd.id')
            ->join('citas as c', 'd.cita_id', '=', 'c.id')
            ->join('usuarios as u', 'c.cliente_id', '=', 'u.id')
            ->whereBetween('p.created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                'u.id',
                'u.nombre',
                'u.email',
                'u.telefono',
                DB::raw('COUNT(DISTINCT p.id) as total_pagos'),
                DB::raw('SUM(p.monto_total) as total_gastado'),
                DB::raw('AVG(p.monto_total) as promedio_por_visita'),
                DB::raw('COUNT(DISTINCT c.id) as total_visitas')
            )
            ->groupBy('u.id', 'u.nombre', 'u.email', 'u.telefono')
            ->orderByDesc('total_gastado')
            ->limit(10)
            ->get();

        $nuevosClientes = DB::table('usuarios')
            ->where('tipo', 'cliente')
            ->whereBetween('created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return [
            'clientes_top' => $clientesTop,
            'nuevos_clientes' => $nuevosClientes,
            'resumen' => [
                'total_clientes_activos' => $clientesTop->count(),
                'cliente_top' => $clientesTop->first(),
                'ingresos_clientes' => $clientesTop->sum('total_gastado')
            ]
        ];
    }

    private function getReporteVehiculos($filtros)
    {
        $marcasMasComunes = DB::table('vehiculos as v')
            ->join('citas as c', 'v.id', '=', 'c.vehiculo_id')
            ->whereBetween('c.created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                'v.marca',
                DB::raw('COUNT(*) as cantidad_visitas'),
                DB::raw('COUNT(DISTINCT v.id) as cantidad_vehiculos')
            )
            ->groupBy('v.marca')
            ->orderByDesc('cantidad_visitas')
            ->get();

        $vehiculosFrecuentes = DB::table('vehiculos as v')
            ->join('citas as c', 'v.id', '=', 'c.vehiculo_id')
            ->whereBetween('c.created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']])
            ->select(
                'v.id',
                'v.placa',
                'v.marca',
                'v.modelo',
                DB::raw('COUNT(c.id) as total_visitas'),
                DB::raw('MAX(c.created_at) as ultima_visita')
            )
            ->groupBy('v.id', 'v.placa', 'v.marca', 'v.modelo')
            ->having('total_visitas', '>', 1)
            ->orderByDesc('total_visitas')
            ->get();

        return [
            'marcas_mas_comunes' => $marcasMasComunes,
            'vehiculos_frecuentes' => $vehiculosFrecuentes,
            'resumen' => [
                'marca_mas_comun' => $marcasMasComunes->first(),
                'total_vehiculos_atendidos' => $marcasMasComunes->sum('cantidad_vehiculos')
            ]
        ];
    }

    private function getEstadisticasGenerales()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        return [
            'ingresos_hoy' => DB::table('pago_detalles')
                ->whereDate('fecha_pago', $hoy)
                ->sum('monto'),
            'ingresos_mes' => DB::table('pago_detalles')
                ->whereBetween('fecha_pago', [$inicioMes, $hoy])
                ->sum('monto'),
            'citas_hoy' => DB::table('citas')
                ->whereDate('fecha', $hoy)
                ->count(),
            'ordenes_pendientes' => DB::table('ordenes_trabajo')
                ->whereIn('estado', ['presupuestada', 'en_proceso'])
                ->count(),
            'pagos_pendientes' => DB::table('pagos')
                ->whereIn('estado', ['pendiente', 'pagado_parcial'])
                ->count(),
            'clientes_activos' => DB::table('usuarios')
                ->where('tipo', 'cliente')
                ->where('estado', 'activo')
                ->count()
        ];
    }

    private function guardarEnCache($filtros, $datos)
    {
        DB::table('reportes_cache')->insert([
            'tipo_reporte' => $filtros['tipo_reporte'],
            'periodo' => $filtros['periodo'],
            'fecha_inicio' => $filtros['fecha_inicio'],
            'fecha_fin' => $filtros['fecha_fin'],
            'datos' => json_encode($datos),
            'created_at' => now()
        ]);
    }

    public function exportar(Request $request)
    {
        $filtros = $request->only(['tipo_reporte', 'fecha_inicio', 'fecha_fin', 'periodo']);

        // Lógica para exportar a PDF o Excel
        // Puedes usar libraries como Laravel Excel o DomPDF

        return response()->json([
            'message' => 'Exportación iniciada',
            'filtros' => $filtros
        ]);
    }
}
