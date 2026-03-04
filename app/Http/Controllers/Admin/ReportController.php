<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\OrdenTrabajo;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use Barryvdh\DomPDF\PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'ordenes' => $this->datosOrdenes($fechaInicio, $fechaFin, $periodo),
            'mecanicos' => $this->datosMecanicos($fechaInicio, $fechaFin),
            default => $this->datosFinancieros($fechaInicio, $fechaFin, $periodo),
        };

        // Asegurar que siempre haya datos para evitar errores en el frontend
        if (!isset($datos['ordenes_por_estado'])) {
            $datos['ordenes_por_estado'] = [];
        }
        if (!isset($datos['ordenes_periodo'])) {
            $datos['ordenes_periodo'] = [];
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
        // Ingresos (pagos completados/terminados)
        $ingresos = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'terminado')
            ->sum('monto');

        // Pendiente por cobrar (pagos no completados)
        $pendiente = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'terminado')
            ->sum('monto');

        // Órdenes completadas
        $ordenesCompletadas = OrdenTrabajo::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'completada')
            ->count();

        // Órdenes pendientes
        $ordenesPendientes = OrdenTrabajo::where('estado', '!=', 'completada')
            ->where('estado', '!=', 'cancelada')
            ->count();

        // Monto promedio por pago
        $pagosTotales = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'terminado')
            ->count();
        $montoPromedio = $pagosTotales > 0 ? $ingresos / $pagosTotales : 0;

        // Ingresos hoy
        $ingresosHoy = Pago::whereDate('created_at', Carbon::today())
            ->where('estado', 'terminado')
            ->sum('monto');

        return [
            'ingresos_totales' => round($ingresos, 2),
            'ingresos_hoy' => round($ingresosHoy, 2),
            'pendiente_cobrar' => round($pendiente, 2),
            'ordenes_completadas' => $ordenesCompletadas,
            'ordenes_pendientes' => $ordenesPendientes,
            'ordenes_pendientes_hoy' => OrdenTrabajo::whereDate('created_at', Carbon::today())->count(),
            'ticket_promedio' => round($montoPromedio, 2),
        ];
    }

    /**
     * Datos financieros para gráficos
     */
    private function datosFinancieros($fechaInicio, $fechaFin, $periodo)
    {
        // Ingresos por método de pago
        $ingresosPorMetodo = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'terminado')
            ->selectRaw('metodopago, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('metodopago')
            ->get()
            ->map(fn($item) => [
                'name' => ucfirst(str_replace('_', ' ', $item->metodopago)),
                'cantidad' => $item->cantidad,
                'total' => round($item->total, 2),
            ]);

        // Ingresos por período (línea temporal)
        $ingresosPeriodo = $this->generarIngresosPeriodo($fechaInicio, $fechaFin, $periodo);

        // Estado de pagos
        $estadoPagos = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as cantidad, SUM(monto) as monto_total')
            ->groupBy('estado')
            ->get()
            ->map(fn($item) => [
                'estado' => $item->estado,
                'cantidad' => $item->cantidad,
                'monto_total' => round($item->monto_total, 2),
            ]);

        // Ingresos vs Pendiente
        $totalIngresos = $ingresosPorMetodo->sum('total');
        $totalPendiente = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'terminado')
            ->sum('monto');

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
     * Datos de órdenes para gráficos
     */
    private function datosOrdenes($fechaInicio, $fechaFin, $periodo)
    {
        // Órdenes por estado
        $ordenesPorEstado = OrdenTrabajo::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->get()
            ->map(fn($item) => [
                'estado' => ucfirst(str_replace('_', ' ', $item->estado)),
                'cantidad' => $item->cantidad,
            ]);

        // Órdenes por período
        $ordenesPeriodo = $this->generarOrdenesPeriodo($fechaInicio, $fechaFin, $periodo);

        // Total de servicios realizados
        $serviciosRealizados = OrdenTrabajo::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->withCount('servicios')
            ->get()
            ->sum('servicios_count');

        return [
            'ordenes_por_estado' => $ordenesPorEstado,
            'ordenes_periodo' => $ordenesPeriodo,
            'servicios_realizados' => $serviciosRealizados,
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
                    $query->where('estado', 'terminado');
                }
            ])
            ->get()
            ->map(function ($mecanico) {
                $ordenes = $mecanico->ordenesTrabajo ?? [];
                $ordenesCompletadas = $ordenes->where('estado', 'completada')->count();
                $ingresos = $ordenes->sum(fn($o) => $o->pagos()->where('estado', 'terminado')->sum('monto'));

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

            $ingresos = Pago::where('estado', 'terminado');

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

            $datos[$key] = round($ingresos->sum('monto'), 2);

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
    /**
     * Generar órdenes por período
     */
    private function generarOrdenesPeriodo($fechaInicio, $fechaFin, $periodo)
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

            $query = OrdenTrabajo::query();

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
     * Exportar reporte en múltiples formatos
     */
    public function exportar(Request $request)
    {
        $formato = $request->input('formato', 'csv');
        $tipoReporte = $request->input('tipo_reporte', 'financiero');
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $fechaInicio = Carbon::createFromFormat('Y-m-d', $fechaInicio);
        $fechaFin = Carbon::createFromFormat('Y-m-d', $fechaFin);

        $datos = $this->obtenerDatosExportacion($tipoReporte, $fechaInicio, $fechaFin);

        return match ($formato) {
            'csv' => $this->exportarCSV($datos, $tipoReporte, $fechaInicio, $fechaFin),
            'pdf' => $this->exportarPDF($datos, $tipoReporte, $fechaInicio, $fechaFin),
            default => $this->exportarCSV($datos, $tipoReporte, $fechaInicio, $fechaFin),
        };
    }

    /**
     * Obtener datos para exportación
     */
    private function obtenerDatosExportacion($tipoReporte, $fechaInicio, $fechaFin)
    {
        $kpis = $this->obtenerKPIs($fechaInicio, $fechaFin);
        
        $datos = match ($tipoReporte) {
            'financiero' => $this->datosFinancieros($fechaInicio, $fechaFin, 'mensual'),
            'servicios' => $this->datosServicios($fechaInicio, $fechaFin, 'mensual'),
            'ordenes' => $this->datosOrdenes($fechaInicio, $fechaFin, 'mensual'),
            'mecanicos' => $this->datosMecanicos($fechaInicio, $fechaFin),
            default => $this->datosFinancieros($fechaInicio, $fechaFin, 'mensual'),
        };

        return [
            'kpis' => $kpis,
            'datos' => $datos,
            'tipoReporte' => $tipoReporte,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ];
    }

    /**
     * Exportar a CSV
     */
    private function exportarCSV($datosExport, $tipoReporte, $fechaInicio, $fechaFin)
    {
        $filename = "reporte_{$tipoReporte}_{$fechaInicio->format('Y-m-d')}_a_{$fechaFin->format('Y-m-d')}.csv";
        
        return response()->stream(
            function () use ($datosExport) {
                if (ob_get_length()) {
                    @ob_end_clean();
                }

                $output = fopen('php://output', 'w');

                // Configurar codificación UTF-8
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

                // Cabecera
                fputcsv($output, ['REPORTE DE ' . strtoupper($datosExport['tipoReporte']), 'TALLER MECÁNICO']);
                fputcsv($output, ['Período:', $datosExport['fechaInicio']->format('d/m/Y') . ' - ' . $datosExport['fechaFin']->format('d/m/Y')]);
                fputcsv($output, ['Fecha de Generación:', Carbon::now()->format('d/m/Y H:i:s')]);
                fputcsv($output, []); // Línea en blanco

                // KPIs
                fputcsv($output, ['INDICADORES CLAVE DE DESEMPEÑO']);
                foreach ($datosExport['kpis'] as $key => $value) {
                    fputcsv($output, [ucfirst(str_replace('_', ' ', $key)), is_numeric($value) ? number_format($value, 2, ',', '.') : $value]);
                }

                fputcsv($output, []); // Línea en blanco
                fputcsv($output, ['DATOS DETALLADOS']);

                // Datos según tipo
                if ($datosExport['tipoReporte'] === 'financiero') {
                    fputcsv($output, ['Método de Pago', 'Cantidad', 'Total']);
                    foreach ($datosExport['datos']['ingresos_por_metodo'] ?? [] as $item) {
                        fputcsv($output, [$item['name'], $item['cantidad'], number_format($item['total'], 2, ',', '.')]);
                    }
                }

                fflush($output);
                fclose($output);
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    /**
     * Exportar a Excel (archivo real .xlsx usando PhpSpreadsheet)
     */
    private function exportarExcel($datosExport, $tipoReporte, $fechaInicio, $fechaFin)
    {
        $filename = "reporte_{$tipoReporte}_{$fechaInicio->format('Y-m-d')}_a_{$fechaFin->format('Y-m-d')}.xlsx";

        return response()->stream(
            function () use ($datosExport) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Cabecera
                $sheet->setCellValue('A1', 'REPORTE DE ' . strtoupper($datosExport['tipoReporte']))
                      ->setCellValue('B1', 'TALLER MECÁNICO');
                $sheet->setCellValue('A2', 'Período:')
                      ->setCellValue('B2', $datosExport['fechaInicio']->format('d/m/Y') . ' - ' . $datosExport['fechaFin']->format('d/m/Y'));
                $sheet->setCellValue('A3', 'Fecha de Generación:')
                      ->setCellValue('B3', Carbon::now()->format('d/m/Y H:i:s'));

                // KPIs
                $row = 5;
                $sheet->setCellValue('A4', 'INDICADORES CLAVE DE DESEMPEÑO');
                foreach ($datosExport['kpis'] as $key => $value) {
                    $label = ucfirst(str_replace('_', ' ', $key));
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("B{$row}", is_numeric($value) ? number_format($value, 2, ',', '.') : $value);
                    $row++;
                }

                // Datos detallados
                $row += 2;
                if ($datosExport['tipoReporte'] === 'financiero') {
                    $sheet->setCellValue("A{$row}", 'Método de Pago');
                    $sheet->setCellValue("B{$row}", 'Cantidad');
                    $sheet->setCellValue("C{$row}", 'Total');
                    $row++;
                    foreach ($datosExport['datos']['ingresos_por_metodo'] ?? [] as $item) {
                        $sheet->setCellValue("A{$row}", $item['name']);
                        $sheet->setCellValue("B{$row}", $item['cantidad']);
                        $sheet->setCellValue("C{$row}", number_format($item['total'], 2, ',', '.'));
                        $row++;
                    }
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    /**
     * Exportar a PDF usando DomPDF
     */
    private function exportarPDF($datosExport, $tipoReporte, $fechaInicio, $fechaFin)
    {
        $filename = "reporte_{$tipoReporte}_{$fechaInicio->format('Y-m-d')}_a_{$fechaFin->format('Y-m-d')}.pdf";
        
        $html = $this->generarHTMLPDF($datosExport);
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download($filename);
    }

    /**
     * Generar HTML para PDF
     */
    private function generarHTMLPDF($datosExport)
    {
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            margin: 20px;
        }
        h1 {
            color: #1a472a;
            text-align: center;
            border-bottom: 3px solid #1a472a;
            padding-bottom: 10px;
            font-size: 24px;
        }
        h2 {
            color: #2d5a3d;
            font-size: 16px;
            margin-top: 20px;
            border-left: 4px solid #2d5a3d;
            padding-left: 10px;
        }
        .info {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .info p {
            margin: 5px 0;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        th {
            background-color: #1a472a;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .kpi-label {
            font-weight: bold;
            width: 40%;
        }
        .kpi-value {
            text-align: right;
            font-weight: bold;
            color: #1a472a;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>';
        
        $html .= '<h1>REPORTE DE ' . strtoupper($datosExport['tipoReporte']) . '</h1>';
        
        $html .= '<div class="info">';
        $html .= '<p><strong>Período:</strong> ' . $datosExport['fechaInicio']->format('d/m/Y') . ' - ' . $datosExport['fechaFin']->format('d/m/Y') . '</p>';
        $html .= '<p><strong>Fecha de Generación:</strong> ' . Carbon::now()->format('d/m/Y H:i:s') . '</p>';
        $html .= '</div>';
        
        $html .= '<h2>INDICADORES CLAVE DE DESEMPEÑO</h2>';
        $html .= '<table>';
        foreach ($datosExport['kpis'] as $key => $value) {
            $label = ucfirst(str_replace('_', ' ', $key));
            $displayValue = is_numeric($value) ? number_format($value, 2, ',', '.') : $value;
            $html .= '<tr>';
            $html .= '<td class="kpi-label">' . $label . '</td>';
            $html .= '<td class="kpi-value">' . $displayValue . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        $html .= '<h2>DATOS DETALLADOS</h2>';
        
        if ($datosExport['tipoReporte'] === 'financiero' && isset($datosExport['datos']['ingresos_por_metodo'])) {
            $html .= '<table>';
            $html .= '<thead><tr><th>Método de Pago</th><th>Cantidad</th><th>Total</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($datosExport['datos']['ingresos_por_metodo'] as $item) {
                $html .= '<tr>';
                $html .= '<td>' . $item['name'] . '</td>';
                $html .= '<td style="text-align: center;">' . $item['cantidad'] . '</td>';
                $html .= '<td style="text-align: right;">' . number_format($item['total'], 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        
        $html .= '<div class="footer">';
        $html .= '<p>Reporte generado automáticamente por el Sistema de Gestión - Taller Mecánico</p>';
        $html .= '</div>';
        
        $html .= '</body></html>';
        
        return $html;
    }
}
