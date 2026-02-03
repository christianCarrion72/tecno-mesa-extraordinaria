<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $ordenes = OrdenTrabajo::with([
            'diagnostico.cita.vehiculo',
            'mecanico',
            'servicios.servicio',
            'pagos'
        ])
            ->whereHas('diagnostico.cita', function ($query) use ($user) {
                $query->where('cliente_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Transformar las órdenes para incluir los costos calculados
        $ordenes->getCollection()->transform(function ($orden) {
            $orden->costo_repuestos = (float) ($orden->costo_repuestos ?? 0);
            $orden->costo_mano_obra = (float) ($orden->costo_mano_obra ?? 0);
            $orden->total = (float) ($orden->total ?? 0);
            $orden->progreso = (int) ($orden->progreso ?? 0);
            return $orden;
        });

        // Calcular estadísticas
        $ordenesQuery = OrdenTrabajo::whereHas('diagnostico.cita', function ($query) use ($user) {
            $query->where('cliente_id', $user->id);
        });

        $estadisticas = [
            'total' => $ordenesQuery->count(),
            'presupuestadas' => (clone $ordenesQuery)->where('estado', 'presupuestada')->count(),
            'en_proceso' => (clone $ordenesQuery)->whereIn('estado', ['aprobada', 'en_proceso'])->count(),
            'completadas' => (clone $ordenesQuery)->whereIn('estado', ['completada', 'entregada'])->count(),
            'orden_activa' => null
        ];

        // Obtener orden activa (en proceso o aprobada más reciente)
        $ordenActiva = OrdenTrabajo::with([
            'diagnostico.cita.vehiculo',
            'mecanico'
        ])
            ->whereHas('diagnostico.cita', function ($query) use ($user) {
                $query->where('cliente_id', $user->id);
            })
            ->whereIn('estado', ['aprobada', 'en_proceso'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($ordenActiva) {
            $estadisticas['orden_activa'] = [
                'id' => $ordenActiva->id,
                'codigo' => $ordenActiva->codigo,
                'progreso' => (int) ($ordenActiva->progreso ?? 0),
                'vehiculo' => [
                    'marca' => $ordenActiva->diagnostico->cita->vehiculo->marca ?? '',
                    'modelo' => $ordenActiva->diagnostico->cita->vehiculo->modelo ?? '',
                    'placa' => $ordenActiva->diagnostico->cita->vehiculo->placa ?? ''
                ],
                'mecanico' => [
                    'nombre' => $ordenActiva->mecanico->nombre ?? 'Sin asignar'
                ]
            ];
        }

        return Inertia::render('Cliente.Ordenes.Index', [
            'ordenes' => $ordenes,
            'filters' => $request->only(['search', 'estado']),
            'estados' => OrdenTrabajo::getEstadosDisponibles(),
            'estadisticas' => $estadisticas,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrdenTrabajo $orden)
    {
        $orden->load([
            'diagnostico.cita.vehiculo',
            'diagnostico.cita.cliente',
            'mecanico',
            'servicios.servicio',
            'pagos.detalles.recibidoPor'
        ]);

        // Transformar los servicios para asegurar que los precios sean números
        $orden->servicios->transform(function ($servicio) {
            $servicio->precio_unitario = (float) $servicio->precio_unitario;
            $servicio->subtotal = (float) $servicio->subtotal;
            return $servicio;
        });

        // Transformar otros campos numéricos
        $orden->costo_mano_obra = (float) $orden->costo_mano_obra;
        $orden->costo_repuestos = (float) $orden->costo_repuestos;

        return Inertia::render('Cliente.Ordenes.Show', [
            'orden' => $orden,
            'progreso' => $orden->progreso,
        ]);
    }

    /**
     * Aprobar orden de trabajo
     */
    public function aprobar(OrdenTrabajo $orden)
    {
        $this->authorize('update', $orden);

        try {
            $orden->marcarComoAprobada();

            return redirect()->back()->with('success', 'Orden aprobada correctamente. El trabajo comenzará pronto.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar la orden: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar orden de trabajo
     */
    public function rechazar(Request $request, OrdenTrabajo $orden)
    {
        $this->authorize('update', $orden);

        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        try {
            $orden->update([
                'estado' => 'cancelada',
                'observaciones' => $request->observaciones . ' (Rechazada por el cliente)',
            ]);

            return redirect()->route('cliente.ordenes.index')->with('success', 'Orden rechazada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al rechazar la orden: ' . $e->getMessage());
        }
    }

    /**
     * Descargar presupuesto en PDF
     */
    public function descargarPresupuesto(OrdenTrabajo $orden)
    {


        $orden->load([
            'diagnostico.cita.vehiculo',
            'diagnostico.cita.cliente',
            'mecanico',
            'servicios.servicio'
        ]);

        // Aquí iría la generación del PDF
        // Por ahora retornamos una vista simple
        return Inertia::render('Cliente.Ordenes.PresupuestoPDF', [
            'orden' => $orden,
        ]);
    }
}
