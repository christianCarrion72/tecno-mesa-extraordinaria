<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\PagoDetalle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Pago::with([
            'ordenTrabajo.diagnostico.cita.vehiculo',
            'ordenTrabajo.diagnostico.cita.cliente',
            'detalles.recibidoPor'
        ])
            ->whereHas('ordenTrabajo.diagnostico.cita', function ($query) use ($user) {
                $query->where('cliente_id', $user->id);
            })
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
                    ->orWhereHas('ordenTrabajo.diagnostico.cita.vehiculo', function ($q) use ($search) {
                        $q->where('placa', 'LIKE', "%{$search}%");
                    });
            });
        }

        $pagos = $query->paginate(12);

        // Transformar datos para el frontend
        $pagos->getCollection()->transform(function ($pago) {
            $pago->monto_total = (float) $pago->monto_total;
            $pago->monto_pagado = (float) $pago->monto_pagado;
            $pago->monto_pendiente = (float) $pago->monto_pendiente;
            $pago->porcentaje_pagado = $this->calcularPorcentajePagado($pago);
            return $pago;
        });

        return Inertia::render('Cliente.Pagos.Index', [
            'pagos' => $pagos,
            'filters' => $request->only(['search', 'estado', 'tipo_pago']),
            'estados' => Pago::getEstadosDisponibles(),
            'tiposPago' => Pago::getTiposPagoDisponibles(),
            'estadisticas' => $this->obtenerEstadisticas($user),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pago $pago)
    {
        $pago->load([
            'ordenTrabajo.diagnostico.cita.vehiculo',
            'ordenTrabajo.diagnostico.cita.cliente',
            'ordenTrabajo.mecanico',
            'ordenTrabajo.servicios.servicio',
            'detalles.recibidoPor'
        ]);

        // Validar que el usuario autenticado sea el cliente dueño de este pago
        $usuarioAutenticado = auth()->user();
        $clientePago = $pago->ordenTrabajo->diagnostico->cita->cliente;

        if ($usuarioAutenticado->id !== $clientePago->id) {
            abort(403, 'No tienes permiso para acceder a este pago');
        }

        // Transformar datos numéricos
        $pago->monto_total = (float) $pago->monto_total;
        $pago->monto_pagado = (float) $pago->monto_pagado;
        $pago->monto_pendiente = (float) $pago->monto_pendiente;
        $pago->porcentaje_pagado = $this->calcularPorcentajePagado($pago);

        return Inertia::render('Cliente.Pagos.Show', [
            'pago' => $pago,
            'planPagos' => $pago->generarPlanPagos(),
            'metodosPago' => PagoDetalle::getMetodosPagoDisponibles(),
        ]);
    }

    /**
     * Mostrar formulario para realizar pago
     */
    public function pagar(Pago $pago)
    {
        $pago->load([
            'ordenTrabajo.diagnostico.cita.vehiculo',
            'ordenTrabajo.diagnostico.cita.cliente'
        ]);

        // Validar que el usuario autenticado sea el cliente dueño de este pago
        $usuarioAutenticado = auth()->user();
        $clientePago = $pago->ordenTrabajo->diagnostico->cita->cliente;

        if ($usuarioAutenticado->id !== $clientePago->id) {
            abort(403, 'No tienes permiso para acceder a este pago');
        }

        // Transformar datos numéricos
        $pago->monto_total = (float) $pago->monto_total;
        $pago->monto_pagado = (float) $pago->monto_pagado;
        $pago->monto_pendiente = (float) $pago->monto_pendiente;

        return Inertia::render('Cliente.Pagos.Pagar', [
            'pago' => $pago,
            'metodosPago' => PagoDetalle::getMetodosPagoDisponibles(),
        ]);
    }

    /**
     * Procesar pago QR usando PagoFácil
     */
    public function procesarQr(Request $request, Pago $pago)
    {
        \Log::info('=== CLIENTE PROCESAR QR ===', ['pago_id' => $pago->id]);

        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $pago->monto_pendiente,
            'banco' => 'required|string',
            'referencia' => 'required|string',
        ]);

        try {
            // Usar el controlador de PagoFácil para generar QR
            $pagoFacilController = new \App\Http\Controllers\Admin\PagoFacilController();

            // Generar QR
            $response = $pagoFacilController->generarQR(
                new Request([
                    'pago_id' => $pago->id,
                    'monto' => $request->monto
                ])
            );

            $data = json_decode($response->getContent(), true);

            if (!$data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Error al generar QR'
                ], 400);
            }

            // Registrar detalles del pago QR
            $pago->detalles()->create([
                'numero_cuota' => $pago->cuotas_pagadas + 1,
                'monto' => $request->monto,
                'metodo_pago' => 'qr',
                'numero_comprobante' => $request->referencia,
                'banco' => $request->banco,
                'referencia' => $data['transaction_id'],
                'recibido_por' => auth()->id(),
                'observaciones' => 'Pago QR iniciado desde portal cliente',
            ]);

            // Actualizar notas del pago con el transaction ID
            $pago->update([
                'notas' => $pago->notas . " | Cliente QR: {$data['transaction_id']}"
            ]);

            \Log::info('QR generado para cliente', [
                'pago_id' => $pago->id,
                'transaction_id' => $data['transaction_id'],
                'monto' => $request->monto
            ]);

            return response()->json([
                'success' => true,
                'qr_image' => $data['qr_image'],
                'transaction_id' => $data['transaction_id'],
                'nro_pago' => $data['nro_pago'],
                'message' => 'Código QR generado. Escanea para completar el pago.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en procesarQr', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el código QR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar pago en efectivo
     */
    public function confirmarEfectivo(Request $request, Pago $pago)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $pago->monto_pendiente,
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            // En un sistema real, esto registraría una solicitud de pago pendiente
            // Por ahora solo simulamos la confirmación
            return response()->json([
                'success' => true,
                'message' => 'Solicitud de pago en efectivo registrada. Por favor acércate al taller para completar el pago.',
                'data' => [
                    'monto' => $request->monto,
                    'pago_id' => $pago->id,
                    'fecha_solicitud' => now()->format('d/m/Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la solicitud de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de pagos del cliente
     */
    private function obtenerEstadisticas($user)
    {
        $pagosQuery = Pago::whereHas('ordenTrabajo.diagnostico.cita', function ($query) use ($user) {
            $query->where('cliente_id', $user->id);
        });

        return [
            'total' => $pagosQuery->count(),
            'pendientes' => (clone $pagosQuery)->pendientes()->count(),
            'parciales' => (clone $pagosQuery)->pagadosParcialmente()->count(),
            'completos' => (clone $pagosQuery)->pagadosTotalmente()->count(),
            'vencidos' => (clone $pagosQuery)->vencidos()->count(),
            'total_pagado' => (clone $pagosQuery)->sum('monto_pagado'),
            'total_pendiente' => (clone $pagosQuery)->sum('monto_pendiente'),
        ];
    }

    /**
     * Calcular porcentaje pagado
     */
    private function calcularPorcentajePagado($pago)
    {
        if ($pago->monto_total == 0)
            return 0;
        return ($pago->monto_pagado / $pago->monto_total) * 100;
    }
}
