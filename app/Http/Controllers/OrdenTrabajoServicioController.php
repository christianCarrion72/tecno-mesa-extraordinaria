<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Servicio;
use App\Models\OrdenTrabajoServicio;
use Illuminate\Http\Request;

class OrdenTrabajoServicioController extends Controller
{
    // AGREGAR SERVICIO A LA ORDEN
    public function store(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para modificar servicios de órdenes de trabajo.');
        }

        // Support multiple input shapes:
        // - 'servicios' => [{servicio_id, cantidad, precio}, ...]  (preferred)
        // - fallback: 'servicio_ids' array + 'cantidad'[, 'precio']
        // - fallback: 'servicio_id' single + 'cantidad'[, 'precio']
        $data = $request->validate([
            'servicios' => 'nullable|array',
            'servicios.*.servicio_id' => 'required_with:servicios|exists:servicios,id',
            'servicios.*.cantidad' => 'required_with:servicios|integer|min:1',
            'servicios.*.precio' => 'nullable|numeric|min:0',

            'servicio_id' => 'nullable|exists:servicios,id',
            'servicio_ids' => 'nullable|array',
            'servicio_ids.*' => 'exists:servicios,id',
            'cantidad' => 'nullable|integer|min:1',
            'precio' => 'nullable|numeric|min:0',
        ]);

        $items = [];

        // preferred: structured array per-item
        if (!empty($data['servicios'])) {
            foreach ($data['servicios'] as $s) {
                // each entry should at least have servicio_id and cantidad (validated by rules)
                $items[] = [
                    'servicio_id' => $s['servicio_id'],
                    'cantidad' => $s['cantidad'],
                    'precio' => isset($s['precio']) ? $s['precio'] : null,
                ];
            }
        } else {
            // fallback to array of ids or single id
            $ids = [];
            if (!empty($data['servicio_ids'])) {
                $ids = $data['servicio_ids'];
            } elseif (!empty($data['servicio_id'])) {
                $ids = [$data['servicio_id']];
            }

            if (empty($ids)) {
                return back()->withErrors(['servicio_ids' => 'Debe seleccionar al menos un servicio.']);
            }

            foreach ($ids as $id) {
                $items[] = [
                    'servicio_id' => $id,
                    'cantidad' => $data['cantidad'] ?? 1,
                    'precio' => isset($data['precio']) ? $data['precio'] : null,
                ];
            }
        }

        // Process each item
        foreach ($items as $item) {
            $id = $item['servicio_id'];
            $servModel = Servicio::findOrFail($id);
            $precioUnit = isset($item['precio']) && $item['precio'] !== '' ? $item['precio'] : $servModel->costo;
            $cantidad = $item['cantidad'];
            $subtotal = $cantidad * $precioUnit;

            $existing = OrdenTrabajoServicio::where('orden_trabajo_id', $ordenTrabajo->id)
                ->where('servicio_id', $id)
                ->first();

            if ($existing) {
                $existing->cantidad = $existing->cantidad + $cantidad;
                if (isset($item['precio']) && $item['precio'] !== null) {
                    $existing->precio = $precioUnit;
                }
                $existing->subtotal = $existing->cantidad * $existing->precio;
                $existing->save();
            } else {
                OrdenTrabajoServicio::create([
                    'orden_trabajo_id' => $ordenTrabajo->id,
                    'servicio_id' => $id,
                    'cantidad' => $cantidad,
                    'precio' => $precioUnit,
                    'subtotal' => $subtotal,
                ]);
            }
        }

        // Recalcular total
        $this->recalcularTotal($ordenTrabajo);

        return back()->with('success', 'Servicios agregados a la orden.');
    }

    // ACTUALIZAR SERVICIO
    public function update(Request $request, OrdenTrabajoServicio $detalle)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para modificar servicios de órdenes de trabajo.');
        }

        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'precio'   => 'required|numeric|min:0',
        ]);

        $data['subtotal'] = $data['cantidad'] * $data['precio'];

        $detalle->update($data);

        $this->recalcularTotal($detalle->ordenTrabajo);

        return back()->with('success', 'Servicio actualizado.');
    }

    // ELIMINAR SERVICIO
    public function destroy(OrdenTrabajoServicio $detalle)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para modificar servicios de órdenes de trabajo.');
        }

        $orden = $detalle->ordenTrabajo;

        $detalle->delete();

        $this->recalcularTotal($orden);

        return back()->with('success', 'Servicio eliminado.');
    }

    // =====================================
    // RE-CALCULAR TOTAL DE LA ORDEN
    // =====================================
    private function recalcularTotal(OrdenTrabajo $orden)
    {
        // IMPORTANTE: tabla correcta = orden_trabajo_servicios
        $total = $orden->servicios()->sum('orden_trabajo_servicios.subtotal');

        $orden->update([
            'total' => $total
        ]);
    }
}
