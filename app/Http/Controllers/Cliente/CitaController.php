<?php
// app/Http/Controllers/Cliente/CitaController.php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Auth::user()->citas()
            ->with(['vehiculo'])
            ->latest()
            ->get();

        $estadisticas = [
            'total' => $citas->count(),
            'pendientes' => $citas->where('estado', 'pendiente')->count(),
            'confirmadas' => $citas->where('estado', 'confirmada')->count(),
            'completadas' => $citas->where('estado', 'completada')->count(),
            'proxima_cita' => $citas->whereIn('estado', ['pendiente', 'confirmada'])
                ->where('fecha', '>=', now()->format('Y-m-d'))
                ->sortBy('fecha')
                ->first()
        ];

        return Inertia::render('Cliente.Citas.Index', [
            'citas' => $citas,
            'estadisticas' => $estadisticas
        ]);
    }

    public function create(Request $request)
    {
        $vehiculos = Auth::user()->vehiculos()
            ->where('estado', 'activo')
            ->get();

        // Validar que el cliente tenga vehículos registrados
        if ($vehiculos->isEmpty()) {
            return redirect()
                ->route('cliente.vehiculos.index')
                ->with('error', 'Debes registrar al menos un vehículo antes de agendar una cita.');
        }

        $vehiculoSeleccionado = $request->get('vehiculo_id')
            ? $vehiculos->firstWhere('id', $request->get('vehiculo_id'))
            : null;

        // Obtener horarios disponibles para los próximos 30 días
        $fechasDisponibles = $this->getFechasDisponibles();
        $horariosDisponibles = $this->getHorariosDisponibles();

        return Inertia::render('Cliente.Citas.Create', [
            'vehiculos' => $vehiculos,
            'vehiculoSeleccionado' => $vehiculoSeleccionado,
            'fechasDisponibles' => $fechasDisponibles,
            'horariosDisponibles' => $horariosDisponibles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date|after:today',
            'hora' => 'required|string',
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar que el vehículo pertenezca al usuario
        $vehiculo = Auth::user()->vehiculos()->findOrFail($request->vehiculo_id);

        // Verificar disponibilidad de la fecha y hora
        if (!$this->validarDisponibilidad($request->fecha, $request->hora)) {
            return back()
                ->withErrors(['hora' => 'El horario seleccionado no está disponible. Por favor elige otro.'])
                ->withInput();
        }

        // Generar código único para la cita
        $codigo = 'CIT-' . strtoupper(uniqid());

        $cita = Cita::create([
            'codigo' => $codigo,
            'cliente_id' => Auth::id(),
            'vehiculo_id' => $request->vehiculo_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones,
            'estado' => 'pendiente'
        ]);

        return redirect()
            ->route('cliente.citas.show', $cita)
            ->with('success', 'Cita agendada exitosamente. Te contactaremos para confirmarla.');
    }

    public function show(Cita $cita)
    {

        $cita->load(['vehiculo', 'diagnostico', 'diagnostico.mecanico']);

        $datosAdicionales = [
            'puede_cancelar' => in_array($cita->estado, ['pendiente', 'confirmada']),
            'puede_reagendar' => in_array($cita->estado, ['pendiente', 'confirmada']),
            'tiene_diagnostico' => !is_null($cita->diagnostico),
            'proximo_estado' => $this->getProximoEstado($cita->estado)
        ];

        return Inertia::render('Cliente.Citas.Show', [
            'cita' => $cita,
            'datosAdicionales' => $datosAdicionales
        ]);
    }

    public function edit(Cita $cita)
    {
        // Solo permitir edición si está pendiente o confirmada
        if (!in_array($cita->estado, ['pendiente', 'confirmada'])) {
            return redirect()
                ->route('cliente.citas.show', $cita)
                ->with('error', 'No se puede editar una cita ' . $cita->estado);
        }

        $vehiculos = Auth::user()->vehiculos()
            ->where('estado', 'activo')
            ->get();

        $fechasDisponibles = $this->getFechasDisponibles();
        $horariosDisponibles = $this->getHorariosDisponibles();

        return Inertia::render('Cliente.Citas.Edit', [
            'cita' => $cita,
            'vehiculos' => $vehiculos,
            'fechasDisponibles' => $fechasDisponibles,
            'horariosDisponibles' => $horariosDisponibles
        ]);
    }

    public function update(Request $request, Cita $cita)
    {
        $this->authorize('update', $cita);

        // Solo permitir actualización si está pendiente o confirmada
        if (!in_array($cita->estado, ['pendiente', 'confirmada'])) {
            return redirect()
                ->route('cliente.citas.show', $cita)
                ->with('error', 'No se puede actualizar una cita ' . $cita->estado);
        }

        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date|after:today',
            'hora' => 'required|string',
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar que el vehículo pertenezca al usuario
        $vehiculo = Auth::user()->vehiculos()->findOrFail($request->vehiculo_id);

        // Verificar disponibilidad (excluyendo la cita actual)
        if (!$this->validarDisponibilidad($request->fecha, $request->hora, $cita->id)) {
            return back()
                ->withErrors(['hora' => 'El horario seleccionado no está disponible. Por favor elige otro.'])
                ->withInput();
        }

        $cita->update([
            'vehiculo_id' => $request->vehiculo_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones,
            'estado' => 'pendiente' // Vuelve a pendiente al reagendar
        ]);

        return redirect()
            ->route('cliente.citas.show', $cita)
            ->with('success', 'Cita actualizada exitosamente.');
    }

    public function destroy(Cita $cita)
    {
        // Solo permitir cancelación si está pendiente o confirmada
        if (!in_array($cita->estado, ['pendiente', 'confirmada'])) {
            return redirect()
                ->route('cliente.citas.show', $cita)
                ->with('error', 'No se puede cancelar una cita ' . $cita->estado);
        }

        $cita->update(['estado' => 'cancelada']);

        return redirect()
            ->route('cliente.citas.index')
            ->with('success', 'Cita cancelada exitosamente.');
    }

    public function cancelar(Cita $cita)
    {

        if (!in_array($cita->estado, ['pendiente', 'confirmada'])) {
            return back()->with('error', 'No se puede cancelar una cita ' . $cita->estado);
        }

        $cita->update(['estado' => 'cancelada']);

        return back()->with('success', 'Cita cancelada exitosamente.');
    }

    private function getFechasDisponibles()
    {
        $fechas = [];
        $inicio = now()->addDay(); // Comenzar desde mañana
        $fin = now()->addDays(30); // Hasta 30 días en el futuro

        for ($fecha = $inicio; $fecha->lte($fin); $fecha->addDay()) {
            // Excluir fines de semana
            if (!$fecha->isWeekend()) {
                $fechas[] = $fecha->format('Y-m-d');
            }
        }

        return $fechas;
    }

    private function getHorariosDisponibles()
    {
        return [
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '14:00',
            '14:30',
            '15:00',
            '15:30',
            '16:00',
            '16:30',
            '17:00'
        ];
    }

    private function validarDisponibilidad($fecha, $hora, $excluirCitaId = null)
    {
        $query = Cita::where('fecha', $fecha)
            ->where('hora', $hora)
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso']);

        if ($excluirCitaId) {
            $query->where('id', '!=', $excluirCitaId);
        }

        return $query->count() === 0;
    }

    private function getProximoEstado($estadoActual)
    {
        $estados = [
            'pendiente' => 'confirmada',
            'confirmada' => 'en_proceso',
            'en_proceso' => 'completada',
            'completada' => null,
            'cancelada' => null
        ];

        return $estados[$estadoActual] ?? null;
    }
}
