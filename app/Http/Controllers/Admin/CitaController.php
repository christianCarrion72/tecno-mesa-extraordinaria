<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CitaController extends Controller
{
    /**
     * Display a listing of the appointments.
     */
    public function index(Request $request)
    {
        $query = Cita::with(['cliente', 'vehiculo']);

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                    ->orWhere('motivo', 'LIKE', "%{$search}%")
                    ->orWhere('observaciones', 'LIKE', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vehiculo', function ($q) use ($search) {
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

        // Filtro por cliente
        if ($request->has('cliente_id') && $request->cliente_id != '') {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro por fecha
        if ($request->has('fecha') && $request->fecha != '') {
            $query->where('fecha', $request->fecha);
        }

        $citas = $query->latest()->paginate(15);

        $clientes = User::clientes()->activos()->get(['id', 'nombre', 'email']);
        $estados = $this->getEstados();

        return Inertia::render('Admin.Citas.Index', [
            'citas' => $citas,
            'clientes' => $clientes,
            'filters' => $request->only(['search', 'estado', 'cliente_id', 'fecha']),
            'estados' => $estados,
        ]);
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $clientes = User::clientes()->activos()->with([
            'vehiculos' => function ($query) {
                $query->activos()->select(['id', 'cliente_id', 'placa', 'marca', 'modelo']);
            }
        ])->get(['id', 'nombre', 'email']);

        return Inertia::render('Admin.Citas.Create', [
            'clientes' => $clientes,
            'estados' => $this->getEstados(), // Asegúrate de que esto retorne un array
            'horarios_disponibles' => $this->getHorariosDisponibles(),
        ]);
    }
    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:usuarios,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|string',
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'estado' => 'required|in:pendiente,confirmada',
        ]);

        // Verificar disponibilidad de la cita
        $citaExistente = Cita::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->exists();

        if ($citaExistente) {
            return redirect()->back()
                ->with('error', 'Ya existe una cita programada para esa fecha y hora. Por favor, seleccione otro horario.');
        }

        // Verificar que el vehículo pertenece al cliente
        $vehiculo = Vehiculo::where('id', $request->vehiculo_id)
            ->where('cliente_id', $request->cliente_id)
            ->first();

        if (!$vehiculo) {
            return redirect()->back()
                ->with('error', 'El vehículo seleccionado no pertenece al cliente.');
        }

        $cita = Cita::create($request->all());

        return redirect()->route('admin.citas.show', $cita->id)
            ->with('success', 'Cita creada exitosamente.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Cita $cita)
    {
        // Cargar relaciones
        $cita->load(['cliente', 'vehiculo', 'diagnostico']);

        // Agregar foto_url al vehículo de la cita
        if ($cita->vehiculo) {
            if ($cita->vehiculo->foto && Storage::disk('public')->exists($cita->vehiculo->foto)) {
                $cita->vehiculo->foto_url = asset('storage/' . $cita->vehiculo->foto);
            } else {
                $cita->vehiculo->foto_url = null;
            }
        }

        return Inertia::render('Admin.Citas.Show', [
            'cita' => $cita,
            'estados' => $this->getEstados(),
        ]);
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Cita $cita)
    {
        // Cargar todas las relaciones necesarias
        $cita->load(['cliente', 'vehiculo', 'diagnostico']);

        $clientes = User::clientes()->activos()->with([
            'vehiculos' => function ($query) {
                $query->activos()->select(['id', 'cliente_id', 'placa', 'marca', 'modelo']);
            }
        ])->get(['id', 'nombre', 'email']);

        return Inertia::render('Admin.Citas.Edit', [
            'cita' => [
                'id' => $cita->id,
                'codigo' => $cita->codigo,
                'cliente_id' => $cita->cliente_id,
                'vehiculo_id' => $cita->vehiculo_id,
                'fecha' => $cita->fecha,
                'hora' => $cita->hora,
                'motivo' => $cita->motivo,
                'observaciones' => $cita->observaciones,
                'estado' => $cita->estado,
                'diagnostico' => $cita->diagnostico ? [
                    'id' => $cita->diagnostico->id,
                    'codigo' => $cita->diagnostico->codigo,
                ] : null,
            ],
            'clientes' => $clientes,
            'estados' => $this->getEstados(),
            'horarios_disponibles' => $this->getHorariosDisponibles(),
        ]);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'cliente_id' => 'required|exists:usuarios,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date',
            'hora' => 'required|string',
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'estado' => 'required|in:pendiente,confirmada,en_proceso,completada,cancelada',
        ]);

        // Verificar disponibilidad de la cita (excluyendo la cita actual)
        $citaExistente = Cita::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('id', '!=', $cita->id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->exists();

        if ($citaExistente) {
            return redirect()->back()
                ->with('error', 'Ya existe una cita programada para esa fecha y hora. Por favor, seleccione otro horario.');
        }

        // Verificar que el vehículo pertenece al cliente
        $vehiculo = Vehiculo::where('id', $request->vehiculo_id)
            ->where('cliente_id', $request->cliente_id)
            ->first();

        if (!$vehiculo) {
            return redirect()->back()
                ->with('error', 'El vehículo seleccionado no pertenece al cliente.');
        }

        $cita->update($request->all());

        return redirect()->route('admin.citas.show', $cita->id)
            ->with('success', 'Cita actualizada exitosamente.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Cita $cita)
    {
        // No permitir eliminar citas que ya están en proceso o tienen diagnóstico
        if ($cita->estado === 'en_proceso' || $cita->diagnostico) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar la cita porque ya está en proceso o tiene un diagnóstico asociado.');
        }

        $cita->delete();

        return redirect()->route('admin.citas.index')
            ->with('success', 'Cita eliminada exitosamente.');
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,en_proceso,completada,cancelada',
        ]);

        $cita->update([
            'estado' => $request->estado,
        ]);

        return redirect()->back()
            ->with('success', 'Estado de la cita actualizado exitosamente.');
    }

    /**
     * Get available time slots
     */
    public function getHorariosDisponibles($fecha = null)
    {
        $horarios = [];
        $horaInicio = 8; // 8:00 AM
        $horaFin = 17;   // 5:00 PM

        for ($hora = $horaInicio; $hora <= $horaFin; $hora++) {
            for ($minuto = 0; $minuto < 60; $minuto += 30) {
                $horario = sprintf('%02d:%02d', $hora, $minuto);
                $horarios[] = $horario;
            }
        }

        return $horarios;
    }

    /**
     * Get appointment statuses
     */
    private function getEstados()
    {
        return [
            'pendiente' => 'Pendiente',
            'confirmada' => 'Confirmada',
            'en_proceso' => 'En Proceso',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
        ];
    }

    /**
     * Get appointments by date
     */
    public function getCitasPorFecha(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
        ]);

        $citas = Cita::with(['cliente', 'vehiculo'])
            ->where('fecha', $request->fecha)
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso'])
            ->orderBy('hora')
            ->get();

        return response()->json($citas);
    }
}
