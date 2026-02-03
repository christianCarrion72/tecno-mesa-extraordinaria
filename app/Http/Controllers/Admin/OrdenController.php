<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\Diagnostico;
use App\Models\User;
use App\Models\Servicio;
use App\Models\OrdenServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrdenController extends Controller
{
    /**
     * Display a listing of work orders.
     */
    public function index(Request $request)
    {
        $query = OrdenTrabajo::with(['diagnostico.cita.cliente', 'diagnostico.cita.vehiculo', 'mecanico', 'servicios.servicio']);

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                    ->orWhereHas('diagnostico.cita.cliente', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('diagnostico.cita.vehiculo', function ($q) use ($search) {
                        $q->where('placa', 'LIKE', "%{$search}%")
                            ->orWhere('marca', 'LIKE', "%{$search}%")
                            ->orWhere('modelo', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por mecánico
        if ($request->has('mecanico_id') && $request->mecanico_id != '') {
            $query->where('mecanico_id', $request->mecanico_id);
        }

        // Filtro por fecha
        if ($request->has('fecha_inicio') && $request->fecha_inicio != '') {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin') && $request->fecha_fin != '') {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_fin);
        }

        $ordenes = $query->latest()->paginate(15);

        $mecanicos = User::mecanicos()->activos()->get(['id', 'nombre']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Ordenes.Index', [
            'ordenes' => $ordenes,
            'mecanicos' => $mecanicos,
            'estados' => $estados,
            'filters' => $request->only(['search', 'estado', 'mecanico_id', 'fecha_inicio', 'fecha_fin']),
        ]);
    }

    /**
     * Show the form for creating a new work order.
     */
    public function create(Request $request)
    {
        $diagnosticoId = $request->get('diagnostico_id');
        $diagnostico = null;

        if ($diagnosticoId) {
            $diagnostico = Diagnostico::with(['cita.cliente', 'cita.vehiculo'])
                ->where('estado', 'aprobado_cliente')
                ->findOrFail($diagnosticoId);

            // Verificar que el diagnóstico no tenga ya una orden de trabajo
            if ($diagnostico->ordenTrabajo) {
                return redirect()->route('admin.ordenes.show', $diagnostico->ordenTrabajo->id)
                    ->with('error', 'Este diagnóstico ya tiene una orden de trabajo asociada.');
            }
        }

        $diagnosticos = Diagnostico::where('estado', 'aprobado_cliente')
            ->whereDoesntHave('ordenTrabajo')
            ->with(['cita.cliente', 'cita.vehiculo'])
            ->orderBy('fecha_diagnostico', 'desc')
            ->get();

        $mecanicos = User::mecanicos()->activos()->get(['id', 'nombre']);
        $servicios = Servicio::activos()->get(['id', 'nombre', 'precio_base', 'duracion_estimada']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Ordenes.Create', [
            'diagnosticos' => $diagnosticos,
            'mecanicos' => $mecanicos,
            'servicios' => $servicios,
            'estados' => $estados,
            'diagnostico_preseleccionado' => $diagnostico,
        ]);
    }

    /**
     * Store a newly created work order in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'diagnostico_id' => 'required|exists:diagnosticos,id',
                'mecanico_id' => 'required|exists:usuarios,id',
                'fecha_creacion' => 'required|date',
                'fecha_fin_estimada' => 'required|date|after_or_equal:fecha_creacion',
                'costo_mano_obra' => 'required|numeric|min:0',
                'estado' => 'required|in:presupuestada,aprobada',
                'observaciones' => 'nullable|string|max:1000',
                'servicios' => 'required|array|min:1',
                'servicios.*.servicio_id' => 'required|exists:servicios,id',
                'servicios.*.cantidad' => 'required|numeric|min:1',
                'servicios.*.precio_unitario' => 'required|numeric|min:0',
                'servicios.*.descripcion' => 'nullable|string|max:500',
            ]);

            // Verificar duplicados
            $diagnostico = Diagnostico::findOrFail($request->diagnostico_id);
            if ($diagnostico->ordenTrabajo) {
                return redirect()->back()
                    ->with('error', 'Este diagnóstico ya tiene una orden de trabajo asociada.');
            }

            DB::beginTransaction();

            // 1. Crear la orden
            $ordenData = [
                'codigo' => $this->generarCodigo(),
                'diagnostico_id' => $request->diagnostico_id,
                'mecanico_id' => $request->mecanico_id,
                'fecha_creacion' => $request->fecha_creacion,
                'fecha_fin_estimada' => $request->fecha_fin_estimada,
                'costo_mano_obra' => $request->costo_mano_obra,
                'costo_repuestos' => 0,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ];

            $orden = OrdenTrabajo::create($ordenData);

            $totalRepuestos = 0;

            // 2. Crear servicios
            foreach ($request->servicios as $index => $servicioData) {

                $servicioOriginal = Servicio::find($servicioData['servicio_id']);
                $descripcion = !empty($servicioData['descripcion']) ? $servicioData['descripcion'] : $servicioOriginal->nombre;
                $subtotalLinea = $servicioData['cantidad'] * $servicioData['precio_unitario'];

                $ordenServicioData = [
                    'orden_trabajo_id' => $orden->id,
                    'servicio_id' => $servicioData['servicio_id'],
                    'descripcion' => $descripcion,
                    'cantidad' => $servicioData['cantidad'],
                    'precio_unitario' => $servicioData['precio_unitario'],
                ];

                OrdenServicio::create($ordenServicioData);

                $totalRepuestos += $subtotalLinea;
            }

            $orden->update(['costo_repuestos' => $totalRepuestos]);

            DB::commit();

            return redirect()->route('admin.ordenes.show', $orden->id)
                ->with('success', 'Orden de trabajo creada exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified work order.
     */
    public function show(OrdenTrabajo $orden)
    {
        $orden->load([
            'diagnostico.cita.cliente',
            'diagnostico.cita.vehiculo.cliente',
            'mecanico',
            'servicios.servicio',
            'pagos.detalles'
        ]);

        return Inertia::render('Admin.Ordenes.Show', [
            'orden' => $orden,
            'estados' => $this->getEstados(),
        ]);
    }

    /**
     * Show the form for editing the specified work order.
     */
    public function edit(OrdenTrabajo $orden)
    {
        $orden->load([
            'diagnostico.cita.cliente',
            'diagnostico.cita.vehiculo',
            'mecanico',
            'servicios.servicio'
        ]);

        $mecanicos = User::mecanicos()->activos()->get(['id', 'nombre']);
        $servicios = Servicio::activos()->get(['id', 'nombre', 'precio_base', 'duracion_estimada']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Ordenes.Edit', [
            'orden' => $orden,
            'mecanicos' => $mecanicos,
            'servicios' => $servicios,
            'estados' => $estados,
        ]);
    }

    /**
     * Update the specified work order in storage.
     */
    public function update(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'mecanico_id' => 'required|exists:usuarios,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin_estimada' => 'required|date',
            'fecha_fin_real' => 'nullable|date',
            'costo_mano_obra' => 'required|numeric|min:0',
            'estado' => 'required|in:presupuestada,aprobada,en_proceso,completada,entregada,cancelada',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Validaciones adicionales según el estado
        if ($request->estado === 'en_proceso' && !$request->fecha_inicio) {
            $request->merge(['fecha_inicio' => now()]);
        }

        if ($request->estado === 'completada' && !$request->fecha_fin_real) {
            $request->merge(['fecha_fin_real' => now()]);
        }

        $orden->update($request->all());

        return redirect()->route('admin.ordenes.show', $orden->id)
            ->with('success', 'Orden de trabajo actualizada exitosamente.');
    }

    /**
     * Update work order status
     */
    public function updateStatus(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'estado' => 'required|in:presupuestada,aprobada,en_proceso,completada,entregada,cancelada',
        ]);

        $data = ['estado' => $request->estado];

        // Lógica adicional según el cambio de estado
        switch ($request->estado) {
            case 'en_proceso':
                $data['fecha_inicio'] = now();
                break;
            case 'completada':
                $data['fecha_fin_real'] = now();
                break;
            case 'cancelada':
                // Podrías agregar lógica adicional para cancelación
                break;
        }

        $orden->update($data);

        return redirect()->back()
            ->with('success', 'Estado de la orden actualizado exitosamente.');
    }

    /**
     * Add service to work order
     */
    public function addService(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $servicio = Servicio::find($request->servicio_id);

            $ordenServicio = OrdenServicio::create([
                'orden_trabajo_id' => $orden->id,
                'servicio_id' => $request->servicio_id,
                'descripcion' => $request->descripcion ?? $servicio->nombre,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
            ]);

            // Actualizar el costo de repuestos
            $nuevoCostoRepuestos = $orden->servicios->sum('subtotal');
            $orden->update(['costo_repuestos' => $nuevoCostoRepuestos]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Servicio agregado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al agregar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Remove service from work order
     */
    public function removeService(OrdenServicio $ordenServicio)
    {
        DB::beginTransaction();

        try {
            $orden = $ordenServicio->ordenTrabajo;
            $ordenServicio->delete();

            // Actualizar el costo de repuestos
            $nuevoCostoRepuestos = $orden->servicios->sum('subtotal');
            $orden->update(['costo_repuestos' => $nuevoCostoRepuestos]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Servicio eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique work order code
     */
    private function generarCodigo()
    {
        $anio = date('Y');
        $mes = date('m');

        $ultimaOrden = OrdenTrabajo::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaOrden) {
            $partes = explode('-', $ultimaOrden->codigo);
            $ultimoNumero = intval(end($partes));
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return "OT-{$anio}{$mes}-" . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get available statuses
     */
    private function getEstados()
    {
        return [
            'presupuestada' => 'Presupuestada',
            'aprobada' => 'Aprobada',
            'en_proceso' => 'En Proceso',
            'completada' => 'Completada',
            'entregada' => 'Entregada',
            'cancelada' => 'Cancelada',
        ];
    }
}
