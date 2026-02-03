<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\OrdenTrabajo;
use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Mostrar el índice de reportes con gráficos
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $periodo = $request->input('periodo', 'mensual');
        $tipoReporte = $request->input('tipo_reporte', 'financiero');

        $fechaInicio = Carbon::createFromFormat('Y-m-d', $fechaInicio)->startOfDay();
        $fechaFin = Carbon::createFromFormat('Y-m-d', $fechaFin)->endOfDay();

        // KPIs Principales
        $kpis = $this->obtenerKPIs($fechaInicio, $fechaFin);

        // Datos para gráficos según tipo
        $datos = match ($tipoReporte) {
            'financiero' => $this->datosFinancieros($fechaInicio, $fechaFin, $periodo),
            'servicios' => $this->datosServicios($fechaInicio, $fechaFin, $periodo),
            'citas' => $this->datosCitas($fechaInicio, $fechaFin, $periodo),
            'mecanicos' => $this->datosMecanicos($fechaInicio, $fechaFin),
            default => $this->datosFinancieros($fechaInicio, $fechaFin, $periodo),
        };

        // Asegurar que siempre haya datos para evitar errores en el frontend
        if (!isset($datos['citas_por_estado'])) {
            $datos['citas_por_estado'] = [];
        }
        if (!isset($datos['citas_periodo'])) {
            $datos['citas_periodo'] = [];
        }
        if (!isset($datos['tasa_conversion_periodo'])) {
            $datos['tasa_conversion_periodo'] = [];
        }

        return Inertia::render('Admin.Reportes.Index', [
            'kpis' => $kpis,
            'datos' => $datos,
            'filtros' => [
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
                'tipo_reporte' => $tipoReporte,
                'periodo' => $periodo,
            ],
        ]);
    }

    /**
     * Obtener KPIs principales
     */
    private function obtenerKPIs($fechaInicio, $fechaFin)
    {
        // Ingresos
        $ingresos = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereIn('estado', ['pagado_total', 'pagado_parcial'])
            ->sum('monto_pagado');

        // Pendiente por cobrar
        $pendiente = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'pagado')
            ->sum('monto_pendiente');

        // Órdenes completadas
        $ordenesCompletadas = OrdenTrabajo::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'completada')
            ->count();

        // Órdenes pendientes
        $ordenesPendientes = OrdenTrabajo::where('estado', '!=', 'completada')
            ->where('estado', '!=', 'cancelada')
            ->count();

        // Citas del período
        $citasTotal = Cita::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();

        // Citas confirmadas
        $citasConfirmadas = Cita::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'confirmada')
            ->count();

        // Tasa de conversión
        $tasaConversion = $citasTotal > 0 ? round(($citasConfirmadas / $citasTotal) * 100, 2) : 0;

        // Ticket promedio
        $ticketPromedio = $ingresos > 0 ? $ingresos / max($ordenesCompletadas, 1) : 0;

        // Ingresos hoy
        $ingresosHoy = Pago::whereDate('created_at', Carbon::today())
            ->whereIn('estado', ['pagado_total', 'pagado_parcial'])
            ->sum('monto_pagado');

        // Citas hoy
        $citasHoy = Cita::whereDate('created_at', Carbon::today())->count();

        return [
            'ingresos_totales' => round($ingresos, 2),
            'ingresos_hoy' => round($ingresosHoy, 2),
            'pendiente_cobrar' => round($pendiente, 2),
            'ordenes_completadas' => $ordenesCompletadas,
            'ordenes_pendientes' => $ordenesPendientes,
            'citas_total' => $citasTotal,
            'citas_confirmadas' => $citasConfirmadas,
            'citas_hoy' => $citasHoy,
            'tasa_conversion' => $tasaConversion,
            'ticket_promedio' => round($ticketPromedio, 2),
        ];
    }

    /**
     * Datos financieros para gráficos
     */
    private function datosFinancieros($fechaInicio, $fechaFin, $periodo)
    {
        // Ingresos por método de pago
        $ingresosPorMetodo = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereIn('estado', ['pagado_total', 'pagado_parcial'])
            ->selectRaw('tipo_pago, COUNT(*) as cantidad, SUM(monto_pagado) as total')
            ->groupBy('tipo_pago')
            ->get()
            ->map(fn($item) => [
                'name' => ucfirst(str_replace('_', ' ', $item->tipo_pago)),
                'cantidad' => $item->cantidad,
                'total' => round($item->total, 2),
            ]);

        // Ingresos por período (línea temporal)
        $ingresosPeriodo = $this->generarIngresosPeriodo($fechaInicio, $fechaFin, $periodo);

        // Estado de pagos
        $estadoPagos = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as cantidad, SUM(monto_total) as monto_total, SUM(monto_pagado) as monto_pagado')
            ->groupBy('estado')
            ->get()
            ->map(fn($item) => [
                'estado' => $item->estado,
                'cantidad' => $item->cantidad,
                'monto_total' => round($item->monto_total, 2),
                'monto_pagado' => round($item->monto_pagado, 2),
            ]);

        // Ingresos vs Pendiente
        $totalIngresos = $ingresosPorMetodo->sum('total');
        $totalPendiente = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'pagado')
            ->sum('monto_pendiente');

        return [
            'ingresos_por_metodo' => $ingresosPorMetodo,
            'ingresos_periodo' => $ingresosPeriodo,
            'estado_pagos' => $estadoPagos,
            'resumen' => [
                'ingresos_totales' => round($totalIngresos, 2),
                'pendiente_total' => round($totalPendiente, 2),
            ],
        ];
    }

    /**
     * Datos de servicios para gráficos
     */
    private function datosServicios($fechaInicio, $fechaFin, $periodo)
    {
        // Servicios más solicitados
        $serviciosMasUsados = Servicio::query()
            ->join('orden_servicios', 'servicios.id', '=', 'orden_servicios.servicio_id')
            ->join('ordenes_trabajo', 'orden_servicios.orden_trabajo_id', '=', 'ordenes_trabajo.id')
            ->whereBetween('ordenes_trabajo.created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('servicios.nombre, servicios.tipo, COUNT(*) as cantidad, SUM(orden_servicios.precio_unitario * orden_servicios.cantidad) as ingresos')
            ->groupBy('servicios.id', 'servicios.nombre', 'servicios.tipo')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'nombre' => $item->nombre,
                'tipo' => $item->tipo,
                'cantidad' => $item->cantidad,
                'ingresos' => round($item->ingresos, 2),
                'precio_promedio' => round($item->ingresos / $item->cantidad, 2),
            ]);

        // Servicios por tipo
        $serviciosPorTipo = Servicio::query()
            ->join('orden_servicios', 'servicios.id', '=', 'orden_servicios.servicio_id')
            ->join('ordenes_trabajo', 'orden_servicios.orden_trabajo_id', '=', 'ordenes_trabajo.id')
            ->whereBetween('ordenes_trabajo.created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('servicios.tipo, COUNT(*) as cantidad, SUM(orden_servicios.precio_unitario * orden_servicios.cantidad) as ingresos')
            ->groupBy('servicios.tipo')
            ->get()
            ->map(fn($item) => [
                'tipo' => ucfirst($item->tipo),
                'cantidad' => $item->cantidad,
                'ingresos' => round($item->ingresos, 2),
            ]);

        // Servicios por período
        $serviciosPeriodo = $this->generarServiciosPeriodo($fechaInicio, $fechaFin, $periodo);

        return [
            'servicios_mas_usados' => $serviciosMasUsados,
            'servicios_por_tipo' => $serviciosPorTipo,
            'servicios_periodo' => $serviciosPeriodo,
        ];
    }

    /**
     * Datos de citas para gráficos
     */
    private function datosCitas($fechaInicio, $fechaFin, $periodo)
    {
        // Citas por estado
        $citasPorEstado = Cita::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->get()
            ->map(fn($item) => [
                'estado' => ucfirst($item->estado),
                'cantidad' => $item->cantidad,
            ]);

        // Citas por período
        $citasPeriodo = $this->generarCitasPeriodo($fechaInicio, $fechaFin, $periodo);

        // Tasa de conversión por período
        $tasaConversionPeriodo = $this->generarTasaConversionPeriodo($fechaInicio, $fechaFin, $periodo);

        // Distribución por hora del día
        $distribucionHora = Cita::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw("TO_CHAR(hora, 'HH24:00') as hora, COUNT(*) as cantidad")
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->map(fn($item) => [
                'hora' => $item->hora ?? 'Sin hora',
                'cantidad' => $item->cantidad,
            ]);

        return [
            'citas_por_estado' => $citasPorEstado,
            'citas_periodo' => $citasPeriodo,
            'tasa_conversion_periodo' => $tasaConversionPeriodo,
            'distribucion_hora' => $distribucionHora,
        ];
    }

    /**
     * Datos de mecánicos para gráficos
     */
    private function datosMecanicos($fechaInicio, $fechaFin)
    {
        // Rendimiento de mecánicos
        $mecanicos = User::where('tipo', 'mecanico')
            ->with([
                'ordenesTrabajo' => function ($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                },
                'ordenesTrabajo.pagos' => function ($query) {
                    $query->whereIn('estado', ['pagado_total', 'pagado_parcial']);
                }
            ])
            ->get()
            ->map(function ($mecanico) {
                $ordenes = $mecanico->ordenesTrabajo ?? [];
                $ordenesCompletadas = $ordenes->where('estado', 'completada')->count();
                $ingresos = $ordenes->sum(fn($o) => $o->pagos->sum('monto_pagado'));

                return [
                    'id' => $mecanico->id,
                    'nombre' => $mecanico->nombre,
                    'total_ordenes' => $ordenes->count(),
                    'ordenes_completadas' => $ordenesCompletadas,
                    'ordenes_pendientes' => $ordenes->where('estado', '!=', 'completada')->count(),
                    'ingresos_generados' => round($ingresos, 2),
                    'promedio_por_orden' => $ordenes->count() > 0 ? round($ingresos / $ordenes->count(), 2) : 0,
                    'tasa_completacion' => $ordenes->count() > 0 ? round(($ordenesCompletadas / $ordenes->count()) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('ingresos_generados')
            ->values();

        return [
            'rendimiento_mecanicos' => $mecanicos,
        ];
    }

    /**
     * Generar ingresos por período
     */
    private function generarIngresosPeriodo($fechaInicio, $fechaFin, $periodo)
    {
        $datos = [];
        $fecha = $fechaInicio->copy();

        while ($fecha <= $fechaFin) {
            $key = match ($periodo) {
                'diario' => $fecha->format('d-m-Y'),
                'semanal' => 'Semana ' . $fecha->format('W'),
                'mensual' => $fecha->format('m-Y'),
                'anual' => $fecha->format('Y'),
                default => $fecha->format('d-m-Y'),
            };

            $ingresos = Pago::whereIn('estado', ['pagado_total', 'pagado_parcial']);

            match ($periodo) {
                'diario' => $ingresos->whereDate('created_at', $fecha),
                'semanal' => $ingresos->whereBetween('created_at', [
                    $fecha->copy()->startOfWeek(),
                    $fecha->copy()->endOfWeek(),
                ]),
                'mensual' => $ingresos->whereYear('created_at', $fecha->year)
                    ->whereMonth('created_at', $fecha->month),
                'anual' => $ingresos->whereYear('created_at', $fecha->year),
            };

            $datos[$key] = round($ingresos->sum('monto_pagado'), 2);

            match ($periodo) {
                'diario' => $fecha->addDay(),
                'semanal' => $fecha->addWeek(),
                'mensual' => $fecha->addMonth(),
                'anual' => $fecha->addYear(),
            };
        }

        return $datos;
    }

    /**
     * Generar servicios por período
     */
    private function generarServiciosPeriodo($fechaInicio, $fechaFin, $periodo)
    {
        $datos = [];
        $fecha = $fechaInicio->copy();

        while ($fecha <= $fechaFin) {
            $key = match ($periodo) {
                'diario' => $fecha->format('d-m-Y'),
                'semanal' => 'Semana ' . $fecha->format('W'),
                'mensual' => $fecha->format('m-Y'),
                'anual' => $fecha->format('Y'),
                default => $fecha->format('d-m-Y'),
            };

            $query = \DB::table('orden_servicios')
                ->join('ordenes_trabajo', 'orden_servicios.orden_trabajo_id', '=', 'ordenes_trabajo.id');

            match ($periodo) {
                'diario' => $query->whereDate('ordenes_trabajo.created_at', $fecha),
                'semanal' => $query->whereBetween('ordenes_trabajo.created_at', [
                    $fecha->copy()->startOfWeek(),
                    $fecha->copy()->endOfWeek(),
                ]),
                'mensual' => $query->whereYear('ordenes_trabajo.created_at', $fecha->year)
                    ->whereMonth('ordenes_trabajo.created_at', $fecha->month),
                'anual' => $query->whereYear('ordenes_trabajo.created_at', $fecha->year),
            };

            $datos[$key] = $query->count();

            match ($periodo) {
                'diario' => $fecha->addDay(),
                'semanal' => $fecha->addWeek(),
                'mensual' => $fecha->addMonth(),
                'anual' => $fecha->addYear(),
            };
        }

        return $datos;
    }

    /**
     * Generar citas por período
     */
    private function generarCitasPeriodo($fechaInicio, $fechaFin, $periodo)
    {
        $datos = [];
        $fecha = $fechaInicio->copy();

        while ($fecha <= $fechaFin) {
            $key = match ($periodo) {
                'diario' => $fecha->format('d-m-Y'),
                'semanal' => 'Semana ' . $fecha->format('W'),
                'mensual' => $fecha->format('m-Y'),
                'anual' => $fecha->format('Y'),
                default => $fecha->format('d-m-Y'),
            };

            $query = Cita::query();

            match ($periodo) {
                'diario' => $query->whereDate('created_at', $fecha),
                'semanal' => $query->whereBetween('created_at', [
                    $fecha->copy()->startOfWeek(),
                    $fecha->copy()->endOfWeek(),
                ]),
                'mensual' => $query->whereYear('created_at', $fecha->year)
                    ->whereMonth('created_at', $fecha->month),
                'anual' => $query->whereYear('created_at', $fecha->year),
            };

            $datos[$key] = $query->count();

            match ($periodo) {
                'diario' => $fecha->addDay(),
                'semanal' => $fecha->addWeek(),
                'mensual' => $fecha->addMonth(),
                'anual' => $fecha->addYear(),
            };
        }

        return $datos;
    }

    /**
     * Generar tasa de conversión por período
     */
    private function generarTasaConversionPeriodo($fechaInicio, $fechaFin, $periodo)
    {
        $datos = [];
        $fecha = $fechaInicio->copy();

        while ($fecha <= $fechaFin) {
            $key = match ($periodo) {
                'diario' => $fecha->format('d-m-Y'),
                'semanal' => 'Semana ' . $fecha->format('W'),
                'mensual' => $fecha->format('m-Y'),
                'anual' => $fecha->format('Y'),
                default => $fecha->format('d-m-Y'),
            };

            $queryTotal = Cita::query();
            $queryConfirmadas = Cita::where('estado', 'confirmada');

            match ($periodo) {
                'diario' => [
                    $queryTotal->whereDate('created_at', $fecha),
                    $queryConfirmadas->whereDate('created_at', $fecha),
                ],
                'semanal' => [
                    $queryTotal->whereBetween('created_at', [$fecha->copy()->startOfWeek(), $fecha->copy()->endOfWeek()]),
                    $queryConfirmadas->whereBetween('created_at', [$fecha->copy()->startOfWeek(), $fecha->copy()->endOfWeek()]),
                ],
                'mensual' => [
                    $queryTotal->whereYear('created_at', $fecha->year)->whereMonth('created_at', $fecha->month),
                    $queryConfirmadas->whereYear('created_at', $fecha->year)->whereMonth('created_at', $fecha->month),
                ],
                'anual' => [
                    $queryTotal->whereYear('created_at', $fecha->year),
                    $queryConfirmadas->whereYear('created_at', $fecha->year),
                ],
            };

            $total = $queryTotal->count();
            $confirmadas = $queryConfirmadas->count();
            $tasa = $total > 0 ? round(($confirmadas / $total) * 100, 2) : 0;
            $datos[$key] = $tasa;

            match ($periodo) {
                'diario' => $fecha->addDay(),
                'semanal' => $fecha->addWeek(),
                'mensual' => $fecha->addMonth(),
                'anual' => $fecha->addYear(),
            };
        }

        return $datos;
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportar(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $fechaInicio = Carbon::createFromFormat('Y-m-d', $fechaInicio);
        $fechaFin = Carbon::createFromFormat('Y-m-d', $fechaFin);

        // Implementar exportación a Excel aquí
        // Por ahora retornamos un JSON con los datos

        return response()->json([
            'mensaje' => 'Exportación en desarrollo',
        ]);
    }
}
