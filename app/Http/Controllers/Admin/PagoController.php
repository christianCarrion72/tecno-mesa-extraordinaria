<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\PagoDetalle;
use App\Models\OrdenTrabajo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pago::with(['ordenTrabajo.diagnostico.cita.cliente', 'ordenTrabajo.diagnostico.cita.vehiculo'])
            ->latest();

        // Filtros
        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tipo_pago') && $request->tipo_pago !== 'todos') {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                    ->orWhereHas('ordenTrabajo.diagnostico.cita.cliente', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('ordenTrabajo.diagnostico.cita.vehiculo', function ($q) use ($search) {
                        $q->where('placa', 'LIKE', "%{$search}%");
                    });
            });
        }

        $pagos = $query->paginate(15);

        return Inertia::render('Admin.Pagos.Index', [
            'pagos' => $pagos,
            'filters' => $request->only(['search', 'estado', 'tipo_pago']),
            'estados' => Pago::getEstadosDisponibles(),
            'tiposPago' => Pago::getTiposPagoDisponibles(),
            'estadisticas' => $this->obtenerEstadisticas(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // Mostrar solo órdenes completadas que no tienen pagos activos
        $ordenes = OrdenTrabajo::where('estado', 'completada')
            ->whereDoesntHave('pagos', function ($query) {
                $query->where('estado', '!=', 'cancelada');
            })
            ->with([
                'diagnostico.cita.cliente',
                'diagnostico.cita.vehiculo',
                'servicios.servicio',
            ])
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return Inertia::render('Admin.Pagos.Create', [
            'ordenes' => $ordenes,
            'tiposPago' => Pago::getTiposPagoDisponibles(),
            'metodosPago' => PagoDetalle::getMetodosPagoDisponibles(),
        ]);
    }    /**
         * Store a newly created resource in storage.
         */
    public function store(Request $request)
    {
        \Log::info('=== INICIANDO CREACIÓN DE PAGO ===');
        \Log::info('Datos recibidos:', $request->all());

        try {
            \Log::info('Iniciando validación...');

            $rules = [
                'orden_trabajo_id' => 'required|exists:ordenes_trabajo,id',
                'tipo_pago' => 'required|in:contado,credito',
                'observaciones' => 'nullable|string|max:500',
            ];

            // Agregar validación condicional para crédito
            if ($request->tipo_pago === 'credito') {
                $rules['numero_cuotas'] = 'required|integer|min:2|max:24';
                $rules['fecha_vencimiento'] = 'required|date|after:today';
            } else {
                // Para contado, numero_cuotas debe ser 1
                $rules['numero_cuotas'] = 'nullable|integer';
            }

            $validated = $request->validate($rules);
            \Log::info('Validación pasada:', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('ERROR en validación:', ['errors' => $e->errors()]);
            throw $e;
        }

        try {
            DB::beginTransaction();
            \Log::info('Transacción iniciada');

            $orden = OrdenTrabajo::with(['diagnostico.cita.cliente'])->findOrFail($request->orden_trabajo_id);
            \Log::info('Orden encontrada:', ['id' => $orden->id, 'estado' => $orden->estado, 'subtotal' => $orden->subtotal]);

            // Verificar que la orden esté completada
            if ($orden->estado !== 'completada') {
                \Log::warning('Orden no está completada', ['estado' => $orden->estado]);
                return back()->with('error', 'Solo se pueden crear pagos para órdenes completadas.');
            }

            // Verificar que no tenga pagos activos
            $pagosActivos = $orden->pagos()->where('estado', '!=', 'cancelada')->exists();
            \Log::info('Verificación de pagos activos:', ['tiene_pagos_activos' => $pagosActivos]);

            if ($pagosActivos) {
                \Log::warning('Orden ya tiene pagos activos');
                return back()->with('error', 'Esta orden ya tiene un pago activo asociado.');
            }

            \Log::info('Creando pago con datos:', [
                'orden_trabajo_id' => $orden->id,
                'monto_total' => $orden->subtotal,
                'tipo_pago' => $request->tipo_pago,
                'numero_cuotas' => $request->tipo_pago === 'credito' ? $request->numero_cuotas : 1,
            ]);

            // Generar código único para el pago
            $codigo = 'PAG-' . date('Ymd') . '-' . str_pad(Pago::count() + 1, 4, '0', STR_PAD_LEFT);
            \Log::info('Código generado:', ['codigo' => $codigo]);

            $pago = Pago::create([
                'codigo' => $codigo,
                'orden_trabajo_id' => $orden->id,
                'monto_total' => $orden->subtotal,
                'tipo_pago' => $request->tipo_pago,
                'numero_cuotas' => $request->tipo_pago === 'credito' ? $request->numero_cuotas : 1,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'observaciones' => $request->observaciones,
            ]);

            \Log::info('Pago creado exitosamente:', ['pago_id' => $pago->id, 'codigo' => $pago->codigo]);

            DB::commit();
            \Log::info('Transacción completada');

            return redirect()->route('admin.pagos.show', $pago->id)
                ->with('success', 'Pago creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ERROR al crear pago:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error al crear el pago: ' . $e->getMessage())->withInput();
        }
    }

    public function cobrar(Pago $pago)
    {
        $pago->load([
            'ordenTrabajo.diagnostico.cita.cliente',
            'ordenTrabajo.diagnostico.cita.vehiculo',
            'detalles.recibidoPor'
        ]);

        return Inertia::render('Admin.Pagos.Cobrar', [
            'pago' => $pago,
            'metodosPago' => PagoDetalle::getMetodosPagoDisponibles(),
            'usuarios' => User::whereIn('tipo', ['secretaria', 'propietario'])->get(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pago $pago)
    {
        $pago->load([
            'ordenTrabajo.diagnostico.cita.cliente',
            'ordenTrabajo.diagnostico.cita.vehiculo',
            'ordenTrabajo.mecanico',
            'detalles.recibidoPor'
        ]);

        return Inertia::render('Admin.Pagos.Show', [
            'pago' => $pago,
            'planPagos' => $pago->generarPlanPagos(),
            'metodosPago' => PagoDetalle::getMetodosPagoDisponibles(),
            'usuarios' => User::whereIn('tipo', ['secretaria', 'propietario'])->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pago $pago)
    {
        $pago->load(['ordenTrabajo.diagnostico.cita.cliente', 'ordenTrabajo.diagnostico.cita.vehiculo']);

        return Inertia::render('Admin.Pagos.Edit', [
            'pago' => $pago,
            'tiposPago' => Pago::getTiposPagoDisponibles(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pago $pago)
    {
        $request->validate([
            'tipo_pago' => 'required|in:contado,credito',
            'numero_cuotas' => 'required_if:tipo_pago,credito|integer|min:1',
            'fecha_vencimiento' => 'required_if:tipo_pago,credito|date|after:today',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $pago->update([
                'tipo_pago' => $request->tipo_pago,
                'numero_cuotas' => $request->tipo_pago === 'credito' ? $request->numero_cuotas : 1,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('admin.pagos.show', $pago->id)
                ->with('success', 'Pago actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el pago: ' . $e->getMessage());
        }
    }
    /**
     * Registrar un pago parcial
     */
    public function registrarPago(Request $request, Pago $pago)
    {
        \Log::info('=== INICIANDO REGISTRO DE PAGO ===');
        \Log::info('Pago ID: ' . $pago->id);
        \Log::info('Datos recibidos: ' . json_encode($request->all()));

        try {
            DB::beginTransaction();

            \Log::info('Estado INICIAL del pago:', [
                'monto_total' => $pago->monto_total,
                'monto_pagado' => $pago->monto_pagado,
                'monto_pendiente' => $pago->monto_pendiente,
                'cuotas_pagadas' => $pago->cuotas_pagadas,
                'estado' => $pago->estado
            ]);

            // Generar número de comprobante si no se proporciona
            $numeroComprobante = $request->numero_comprobante;
            if (empty($numeroComprobante)) {
                $prefix = $request->metodo_pago === 'efectivo' ? 'EF' : 'QR';
                $fecha = now()->format('Ymd');
                $secuencia = PagoDetalle::where('metodo_pago', $request->metodo_pago)
                    ->whereDate('created_at', now())
                    ->count() + 1;
                $numeroComprobante = $prefix . '-' . $fecha . '-' . str_pad($secuencia, 4, '0', STR_PAD_LEFT);
            }

            \Log::info('Número de comprobante: ' . $numeroComprobante);

            // Calcular nuevo número de cuota
            $nuevoNumeroCuota = $pago->cuotas_pagadas + 1;
            \Log::info('Nuevo número de cuota: ' . $nuevoNumeroCuota);

            // Crear el detalle del pago
            \Log::info('Creando detalle de pago...');
            $detallePago = $pago->detalles()->create([
                'numero_cuota' => $nuevoNumeroCuota,
                'monto' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
                'numero_comprobante' => $numeroComprobante,
                'banco' => $request->banco,
                'referencia' => $request->referencia,
                'recibido_por' => $request->recibido_por,
                'fecha_pago' => $request->fecha_pago ?? now()->format('Y-m-d'),
                'hora_pago' => $request->hora_pago ?? now()->format('H:i:s'),
                'observaciones' => $request->observaciones,
            ]);


            // ACTUALIZAR MANUALMENTE EL PAGO PRINCIPAL
            $nuevoMontoPagado = $pago->monto_pagado + $request->monto;
            $nuevasCuotasPagadas = $pago->cuotas_pagadas + 1;

            \Log::info('Cálculos de actualización:', [
                'monto_actual_pagado' => $pago->monto_pagado,
                'monto_nuevo_pago' => $request->monto,
                'nuevo_monto_total_pagado' => $nuevoMontoPagado,
                'nuevo_numero_cuotas' => $nuevasCuotasPagadas
            ]);

            // Determinar el nuevo estado
            $nuevoEstado = $pago->estado;
            if ($nuevoMontoPagado >= $pago->monto_total) {
                $nuevoEstado = 'pagado_total';
                \Log::info('Pago completado totalmente');
            } elseif ($nuevoMontoPagado > 0 && $nuevoMontoPagado < $pago->monto_total) {
                $nuevoEstado = 'pagado_parcial';
                \Log::info('Pago parcial registrado');
            }
            // Actualizar el pago principal
            $pago->update([
                'monto_pagado' => $nuevoMontoPagado,
                'cuotas_pagadas' => $nuevasCuotasPagadas,
                'estado' => $nuevoEstado,
            ]);
            DB::commit();
            return redirect()->route('admin.pagos.show', $pago->id)
                ->with('success', 'Pago registrado exitosamente. Se ha abonado ' .
                    number_format($request->monto, 2) . ' Bs. al pago.');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('ERROR en registrarPago: ' . $e->getMessage());
            \Log::error('Trace completo: ' . $e->getTraceAsString());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());

            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Obtener estadísticas de pagos
     */
    private function obtenerEstadisticas()
    {
        return [
            'total' => Pago::count(),
            'pendientes' => Pago::pendientes()->count(),
            'parciales' => Pago::pagadosParcialmente()->count(),
            'completos' => Pago::pagadosTotalmente()->count(),
            'vencidos' => Pago::vencidos()->count(),
            'ingresos_hoy' => PagoDetalle::delDia()->sum('monto'),
            'ingresos_mes' => PagoDetalle::delMesActual()->sum('monto'),
        ];
    }

    /**
     * Obtener pagos vencidos
     */
    public function vencidos()
    {
        $pagos = Pago::vencidos()
            ->with(['ordenTrabajo.diagnostico.cita.cliente', 'ordenTrabajo.diagnostico.cita.vehiculo'])
            ->paginate(15);

        return Inertia::render('Admin.Pagos.Vencidos', [
            'pagos' => $pagos,
            'estadisticas' => $this->obtenerEstadisticas(),
        ]);
    }

    /**
     * Generar reporte de pagos
     */
    public function reporte(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_reporte' => 'required|in:diario,semanal,mensual,anual',
        ]);

        $pagos = Pago::with(['ordenTrabajo.diagnostico.cita.cliente', 'detalles'])
            ->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin])
            ->get();

        $detallesPagos = PagoDetalle::with(['pago.ordenTrabajo.diagnostico.cita.cliente', 'recibidoPor'])
            ->whereBetween('fecha_pago', [$request->fecha_inicio, $request->fecha_fin])
            ->get();

        return Inertia::render('Admin.Pagos.Reporte', [
            'pagos' => $pagos,
            'detallesPagos' => $detallesPagos,
            'filtros' => $request->only(['fecha_inicio', 'fecha_fin', 'tipo_reporte']),
            'estadisticas' => Pago::obtenerEstadisticas($request->fecha_inicio, $request->fecha_fin),
        ]);
    }
}
