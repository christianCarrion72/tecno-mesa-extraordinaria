<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnostico;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DiagnosticoController extends Controller
{
    /**
     * Display a listing of diagnostics.
     */
    public function index(Request $request)
    {
        $query = Diagnostico::with(['cita.cliente', 'cita.vehiculo', 'mecanico']);

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion_problema', 'LIKE', "%{$search}%")
                    ->orWhere('diagnostico', 'LIKE', "%{$search}%")
                    ->orWhereHas('cita.cliente', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('cita.vehiculo', function ($q) use ($search) {
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
        if ($request->has('fecha') && $request->fecha != '') {
            $query->whereDate('fecha_diagnostico', $request->fecha);
        }

        $diagnosticos = $query->latest()->paginate(15);

        $mecanicos = User::mecanicos()->activos()->get(['id', 'nombre']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Diagnosticos.Index', [
            'diagnosticos' => $diagnosticos,
            'mecanicos' => $mecanicos,
            'estados' => $estados,
            'filters' => $request->only(['search', 'estado', 'mecanico_id', 'fecha']),
        ]);
    }

    /**
     * Show the form for creating a new diagnostic.
     */
    public function create(Request $request)
    {
        // Obtener la cita si se pasa por parámetro
        $citaId = $request->get('cita_id');
        $cita = null;

        if ($citaId) {
            $cita = Cita::with(['cliente', 'vehiculo'])->findOrFail($citaId);

            // Verificar que la cita no tenga ya un diagnóstico
            if ($cita->diagnostico) {
                return redirect()->route('admin.diagnosticos.show', $cita->diagnostico->id)
                    ->with('error', 'Esta cita ya tiene un diagnóstico asociado.');
            }

            // Verificar que la cita esté en estado apropiado
            if (!in_array($cita->estado, ['confirmada', 'en_proceso'])) {
                return redirect()->route('admin.citas.show', $cita->id)
                    ->with('error', 'Solo se puede crear un diagnóstico para citas confirmadas o en proceso.');
            }
        }

        $citas = Cita::whereDoesntHave('diagnostico')
            // Agregamos 'pendiente' solo para probar si aparecen
            ->whereIn('estado', ['confirmada', 'en_proceso', 'Confirmada', 'En_proceso', 'pendiente'])
            ->with(['cliente', 'vehiculo'])
            ->orderBy('fecha', 'desc')
            ->get();

        $mecanicos = User::mecanicos()->activos()->get(['id', 'nombre']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Diagnosticos.Create', [
            'citas' => $citas,
            'mecanicos' => $mecanicos,
            'estados' => $estados, // Esta línea es crucial
            'cita_preseleccionada' => $cita,
        ]);
    }



    /**
     * Store a newly created diagnostic in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'mecanico_id' => 'required|exists:usuarios,id',
            'fecha_diagnostico' => 'required|date',
            'descripcion_problema' => 'required|string|max:2000',
            'diagnostico' => 'required|string|max:2000',
            'recomendaciones' => 'nullable|string|max:2000',
            'estado' => 'required|in:en_revision,completado',
        ]);

        // Verificar que la cita no tenga ya un diagnóstico
        $cita = Cita::findOrFail($request->cita_id);
        if ($cita->diagnostico) {
            return redirect()->back()
                ->with('error', 'Esta cita ya tiene un diagnóstico asociado.');
        }

        // Verificar que el mecánico sea válido
        $mecanico = User::mecanicos()->findOrFail($request->mecanico_id);

        // Crear el diagnóstico
        $diagnostico = Diagnostico::create([
            'codigo' => $this->generarCodigo(),
            'cita_id' => $request->cita_id,
            'mecanico_id' => $request->mecanico_id,
            'fecha_diagnostico' => $request->fecha_diagnostico,
            'descripcion_problema' => $request->descripcion_problema,
            'diagnostico' => $request->diagnostico,
            'recomendaciones' => $request->recomendaciones,
            'estado' => $request->estado,
        ]);

        // Actualizar el estado de la cita a 'en_proceso' si está 'confirmada'
        if ($cita->estado === 'confirmada') {
            $cita->update(['estado' => 'en_proceso']);
        }

        return redirect()->route('admin.diagnosticos.show', $diagnostico->id)
            ->with('success', 'Diagnóstico creado exitosamente.');
    }

    public function edit(Diagnostico $diagnostico)
    {
        // Cargar relaciones necesarias para la vista de edición
        // Necesitamos cita.vehiculo y cita.cliente para la tarjeta de contexto
        $diagnostico->load(['cita.cliente', 'cita.vehiculo']);

        $mecanicos = User::mecanicos()->activos()->get(['id', 'nombre']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Diagnosticos.Edit', [
            'diagnostico' => $diagnostico,
            'mecanicos' => $mecanicos,
            'estados' => $estados,
        ]);
    }

    public function update(Request $request, Diagnostico $diagnostico)
    {
        $request->validate([
            'mecanico_id' => 'required|exists:usuarios,id',
            'fecha_diagnostico' => 'required|date',
            'descripcion_problema' => 'required|string|max:2000',
            'diagnostico' => 'required|string|max:2000',
            'recomendaciones' => 'nullable|string|max:2000',
            'estado' => 'required|in:en_revision,completado,aprobado_cliente,rechazado_cliente',
        ]);

        $diagnostico->update($request->all());

        return redirect()->route('admin.diagnosticos.show', $diagnostico->id)
            ->with('success', 'Diagnóstico actualizado correctamente.');
    }

    public function show(Diagnostico $diagnostico)
    {
        // Cargamos todas las relaciones que la vista Show.vue necesita
        $diagnostico->load([
            'cita.cliente',   // Para mostrar nombre, email, teléfono del cliente
            'cita.vehiculo',  // Para mostrar marca, modelo, placa, color
            'mecanico',       // Para mostrar el nombre del técnico responsable
            'ordenTrabajo'    // Para saber si mostramos el botón "Crear Orden" o no
        ]);

        return Inertia::render('Admin.Diagnosticos.Show', [
            'diagnostico' => $diagnostico,
            'estados' => $this->getEstados(), // Pasamos los estados para el dropdown lateral
        ]);
    }

    /**
     * Remove the specified diagnostic from storage.
     */
    public function destroy(Diagnostico $diagnostico)
    {
        $diagnostico->delete();

        return redirect()->route('admin.diagnosticos.index')
            ->with('success', 'Diagnóstico eliminado exitosamente.');
    }
    /**
     * Get available statuses
     */
    private function getEstados()
    {
        return [
            'en_revision' => 'En Revisión',
            'completado' => 'Completado',
            'aprobado_cliente' => 'Aprobado por Cliente',
            'rechazado_cliente' => 'Rechazado por Cliente',
        ];
    }

    /**
     * Generar un código único para el diagnóstico.
     * Formato: DG-00001
     */
    private function generarCodigo()
    {
        // Obtenemos el último diagnóstico registrado en la base de datos
        $ultimoDiagnostico = Diagnostico::latest('id')->first();

        if (!$ultimoDiagnostico) {
            // Si no existe ninguno, este es el primero
            $numero = 1;
        } else {
            // Extraemos el número del código anterior (asumiendo formato DG-XXXXX)
            // substr($string, 3) elimina "DG-" y nos deja solo el número
            $numero = intval(substr($ultimoDiagnostico->codigo, 3)) + 1;
        }

        // Retornamos el código formateado: DG- + el número relleno con ceros a la izquierda
        return 'DG-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}
