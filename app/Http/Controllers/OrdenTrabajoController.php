<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Motor;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class OrdenTrabajoController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar órdenes de trabajo.');
        }

        $query = OrdenTrabajo::with(['cliente', 'usuario', 'motor']);

        $busqueda = $request->input('busqueda');

        if ($busqueda) {
            // normalize search term (remove accents, lower)
            $search = Str::ascii(Str::lower($busqueda));

            // helper: chain of REPLACE to remove accents from column values
            $replacements = [
                'á' => 'a','à' => 'a','ä' => 'a','â' => 'a','ã' => 'a','å' => 'a',
                'Á' => 'a','À' => 'a','Ä' => 'a','Â' => 'a','Ã' => 'a','Å' => 'a',
                'é' => 'e','è' => 'e','ë' => 'e','ê' => 'e','É' => 'e','È' => 'e','Ë' => 'e','Ê' => 'e',
                'í' => 'i','ì' => 'i','ï' => 'i','î' => 'i','Í' => 'i','Ì' => 'i','Ï' => 'i','Î' => 'i',
                'ó' => 'o','ò' => 'o','ö' => 'o','ô' => 'o','õ' => 'o','Ó' => 'o','Ò' => 'o','Ö' => 'o','Ô' => 'o','Õ' => 'o',
                'ú' => 'u','ù' => 'u','ü' => 'u','û' => 'u','Ú' => 'u','Ù' => 'u','Ü' => 'u','Û' => 'u',
                'ñ' => 'n','Ñ' => 'n','ç' => 'c','Ç' => 'c'
            ];

            $buildExpr = function ($column) use ($replacements) {
                $expr = $column;
                foreach ($replacements as $from => $to) {
                    // each replacement wraps previous expr
                    $expr = "REPLACE($expr, '$from', '$to')";
                }
                return "LOWER($expr)";
            };

            $clienteExpr = $buildExpr('nombre');
            $descripcionExpr = $buildExpr('descripcion');

            $query->where(function ($q) use ($search, $clienteExpr, $descripcionExpr) {
                // search in cliente.nombre (with accent-insensitive normalized comparison)
                $q->whereHas('cliente', function ($qq) use ($search, $clienteExpr) {
                    $qq->whereRaw("$clienteExpr LIKE ?", ["%$search%"]);
                });

                // or search in orden.descripcion
                $q->orWhereRaw("$descripcionExpr LIKE ?", ["%$search%"]);
            });
        }

        $ordenes = $query->orderBy('id', 'desc')->paginate(10);

        return inertia('OrdenTrabajo/Index', [
            'ordenes' => $ordenes,
            'terminosBusqueda' => $busqueda,
        ]);
    }

    public function create()
    {
        if (!request()->user()->tienePermiso('orden_trabajo.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear órdenes de trabajo.');
        }

        return inertia('OrdenTrabajo/Create', [
            // ✔ Cliente en formato para ComboBox
            'clientes' => Cliente::select('id', 'nombre')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'label' => $c->nombre
                ]),

            // ✔ Usuarios en formato ComboBox
            'usuarios' => User::select('id', 'name')
                ->get()
                ->map(fn($u) => [
                    'id' => $u->id,
                    'label' => $u->name
                ]),

            // ✔ Motores mostrando Marca + Modelo + Número de Serie
            'motores' => Motor::with(['marca', 'modelo'])
                ->get()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'label' => "{$m->marca->nombre} - {$m->modelo->nombre} ({$m->numero_serie})"
                ]),

            'estados' => ['pendiente','aprobado','proceso','terminado'],
        ]);
    }

    public function store(Request $request)
    {
        // El formulario de creación no expone ni permite modificar el estado ni la fecha de fin.
        // Se fijan valores seguros aquí para evitar validaciones fallidas por datos faltantes.
        $request->merge([
            'estado' => 'pendiente',
            'fechafin' => null,
        ]);

        // Si no se proporciona fecha de inicio, la usamos como hoy.
        if (!$request->filled('fechainicio')) {
            $request->merge(['fechainicio' => now()->toDateString()]);
        }

        $data = $request->validate([
            'fechainicio' => 'required|date',
            'fechafin' => 'nullable|date',
            'descripcion' => 'required|string',
            'total' => 'sometimes|numeric|min:0',
            'estado' => 'required|in:pendiente,aprobado,proceso,terminado',
            'cliente_id' => 'required|exists:clientes,id',
            'usuario_id' => 'required|exists:users,id',
            'motor_id' => 'required|exists:motores,id',
        ]);

        $data['total'] = 0;
        OrdenTrabajo::create($data);

        return redirect()->route('orden-trabajos.index')
            ->with('success', 'Orden creada correctamente.');
    }

    public function edit(OrdenTrabajo $ordenTrabajo)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar órdenes de trabajo.');
        }

        return inertia('OrdenTrabajo/Edit', [
            'orden' => $ordenTrabajo->load(['cliente', 'usuario', 'motor']),

            // ✔ mismo formato que create()
            'clientes' => Cliente::select('id', 'nombre')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'label' => $c->nombre
                ]),

            'usuarios' => User::select('id', 'name')
                ->get()
                ->map(fn($u) => [
                    'id' => $u->id,
                    'label' => $u->name
                ]),

            'motores' => Motor::with(['marca', 'modelo'])
                ->get()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'label' => "{$m->marca->nombre} - {$m->modelo->nombre} ({$m->numero_serie})"
                ]),

            'estados' => ['pendiente','aprobado','proceso','terminado'],
        ]);
    }

    public function update(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $data = $request->validate([
            'fechainicio' => 'required|date',
            'fechafin' => 'nullable|date',
            'descripcion' => 'required|string',
            'total' => 'sometimes|numeric|min:0',
            'estado' => 'required',
            'cliente_id' => 'required|exists:clientes,id',
            'usuario_id' => 'required|exists:users,id',
            'motor_id' => 'required|exists:motores,id',
        ]);

        // Si el formulario no envía total (por ejemplo porque no se muestra), mantenemos el actual.
        if (!$request->has('total')) {
            $data['total'] = $ordenTrabajo->total;
        }

        $ordenTrabajo->update($data);

        return redirect()->route('orden-trabajos.index')->with('success', 'Orden actualizada.');
    }

    public function show(OrdenTrabajo $ordenTrabajo)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para ver órdenes de trabajo.');
        }

        $ordenTrabajo->load([
           'cliente',
           'usuario',
           'motor',
           'servicios' => function ($q) {
               $q->withPivot(['cantidad', 'precio', 'subtotal']);
            },
           'incidencias',
           'planPago',
        ]);

         $serviciosCatalogo = Servicio::select('id', 'nombre', 'costo')
           ->orderBy('nombre')
           ->get();

        return inertia('OrdenTrabajo/Show', [
            'orden' => $ordenTrabajo,
            'serviciosCatalogo' => $serviciosCatalogo,
        ]);
    }

    public function destroy(OrdenTrabajo $ordenTrabajo)
    {
        if (!request()->user()->tienePermiso('orden_trabajo.eliminar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para eliminar órdenes de trabajo.');
        }

        $ordenTrabajo->delete();

        return back()->with('success', 'Orden eliminada.');
    }
}
