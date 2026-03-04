<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!request()->user()->tienePermiso('factura.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar facturas.');
        }

        return redirect()->route('plan-pagos.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Pago $pago)
    {
        if (!request()->user()->tienePermiso('factura.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear facturas.');
        }

        if ($pago->factura) {
            return redirect()->route('facturas.show', $pago->factura->id)
                ->with('error', 'Este pago ya tiene una factura.');
        }

        return Inertia::render('Facturas/Create', [
            'pago' => $pago,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Pago $pago)
    {
        if (!request()->user()->tienePermiso('factura.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear facturas.');
        }

        if ($pago->factura) {
            return redirect()->route('facturas.show', $pago->factura->id)
                ->with('error', 'Este pago ya tiene una factura.');
        }

        $data = $request->validate([
            'descripcion' => 'required|string',
            'estado' => 'required|string',
            'fechaemision' => 'required|date',
            'montototal' => 'nullable|numeric|min:0',
            'nroautorizacion' => 'required|string',
            'numerofactura' => 'required|string',
        ]);

        $data['montototal'] = $data['montototal'] ?? $pago->monto;
        $data['pago_id'] = $pago->id;

        $factura = Factura::create($data);

        return redirect()->route('facturas.show', $factura->id)
            ->with('success', 'Factura emitida correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Factura $factura)
    {
        if (!request()->user()->tienePermiso('factura.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para ver facturas.');
        }

        $factura->load(['pago.planPago', 'pago.planPago.ordenTrabajo', 'pago.planPago.ordenTrabajo.cliente']);
        return Inertia::render('Facturas/Show', [
            'factura' => $factura,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
        if (!request()->user()->tienePermiso('factura.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar facturas.');
        }

        return redirect()->route('facturas.show', $factura->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        if (!request()->user()->tienePermiso('factura.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar facturas.');
        }

        return redirect()->route('facturas.show', $factura->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura)
    {
        if (!request()->user()->tienePermiso('factura.eliminar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para eliminar facturas.');
        }

        $factura->delete();
        return redirect()->route('plan-pagos.index')
            ->with('success', 'Factura eliminada.');
    }
}
