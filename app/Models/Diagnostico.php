<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';

    protected $fillable = [
        'codigo',
        'cita_id',
        'mecanico_id',
        'fecha_diagnostico',
        'descripcion_problema',
        'diagnostico',
        'recomendaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_diagnostico' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Relación con la cita
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    /**
     * Relación con el mecánico que realizó el diagnóstico
     */
    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    /**
     * Relación con la orden de trabajo (uno a uno)
     */
    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajo::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope para diagnosticos en revisión
     */
    public function scopeEnRevision($query)
    {
        return $query->where('estado', 'en_revision');
    }

    /**
     * Scope para diagnosticos completados
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    /**
     * Scope para diagnosticos aprobados por el cliente
     */
    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado_cliente');
    }

    /**
     * Scope para diagnosticos rechazados por el cliente
     */
    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado_cliente');
    }

    /**
     * Scope para diagnosticos de un mecánico específico
     */
    public function scopeDelMecanico($query, $mecanicoId)
    {
        return $query->where('mecanico_id', $mecanicoId);
    }

    /**
     * Scope para diagnosticos de una fecha específica
     */
    public function scopeDeFecha($query, $fecha)
    {
        return $query->where('fecha_diagnostico', $fecha);
    }

    /**
     * Scope para diagnosticos del mes actual
     */
    public function scopeDelMesActual($query)
    {
        return $query->whereYear('fecha_diagnostico', now()->year)
                    ->whereMonth('fecha_diagnostico', now()->month);
    }

    // =========================================================================
    // MÉTODOS DE UTILIDAD
    // =========================================================================

    /**
     * Verificar si el diagnóstico está en revisión
     */
    public function estaEnRevision()
    {
        return $this->estado === 'en_revision';
    }

    /**
     * Verificar si el diagnóstico está completado
     */
    public function estaCompletado()
    {
        return $this->estado === 'completado';
    }

    /**
     * Verificar si el diagnóstico está aprobado por el cliente
     */
    public function estaAprobado()
    {
        return $this->estado === 'aprobado_cliente';
    }

    /**
     * Verificar si el diagnóstico está rechazado por el cliente
     */
    public function estaRechazado()
    {
        return $this->estado === 'rechazado_cliente';
    }

    /**
     * Marcar diagnóstico como completado
     */
    public function marcarComoCompletado()
    {
        $this->update(['estado' => 'completado']);
        return $this;
    }

    /**
     * Marcar diagnóstico como aprobado por el cliente
     */
    public function marcarComoAprobado()
    {
        $this->update(['estado' => 'aprobado_cliente']);
        return $this;
    }

    /**
     * Marcar diagnóstico como rechazado por el cliente
     */
    public function marcarComoRechazado()
    {
        $this->update(['estado' => 'rechazado_cliente']);
        return $this;
    }

    /**
     * Obtener el cliente asociado al diagnóstico
     */
    public function getClienteAttribute()
    {
        return $this->cita->cliente;
    }

    /**
     * Obtener el vehículo asociado al diagnóstico
     */
    public function getVehiculoAttribute()
    {
        return $this->cita->vehiculo;
    }

    /**
     * Verificar si el diagnóstico tiene una orden de trabajo asociada
     */
    public function tieneOrdenTrabajo()
    {
        return $this->ordenTrabajo()->exists();
    }

    /**
     * Obtener información resumida del diagnóstico
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'codigo' => $this->codigo,
            'fecha' => $this->fecha_diagnostico->format('d/m/Y'),
            'cliente' => $this->cita->cliente->nombre,
            'vehiculo' => $this->cita->vehiculo->marca . ' ' . $this->cita->vehiculo->modelo,
            'mecanico' => $this->mecanico->nombre,
            'estado' => $this->estado,
        ];
    }

    /**
     * Obtener los estados disponibles para un diagnóstico
     */
    public static function getEstadosDisponibles()
    {
        return [
            'en_revision' => 'En Revisión',
            'completado' => 'Completado',
            'aprobado_cliente' => 'Aprobado por Cliente',
            'rechazado_cliente' => 'Rechazado por Cliente',
        ];
    }

    /**
     * Obtener el texto legible del estado
     */
    public function getEstadoTextoAttribute()
    {
        $estados = self::getEstadosDisponibles();
        return $estados[$this->estado] ?? $this->estado;
    }

    /**
     * Obtener diagnosticos pendientes de aprobación
     */
    public static function pendientesAprobacion()
    {
        return self::where('estado', 'completado')
                  ->whereDoesntHave('ordenTrabajo')
                  ->get();
    }

    /**
     * Buscar diagnósticos por código, cliente o vehículo
     */
    public static function buscar($termino)
    {
        return self::where('codigo', 'LIKE', "%{$termino}%")
                ->orWhereHas('cita.cliente', function($query) use ($termino) {
                    $query->where('nombre', 'LIKE', "%{$termino}%");
                })
                ->orWhereHas('cita.vehiculo', function($query) use ($termino) {
                    $query->where('marca', 'LIKE', "%{$termino}%")
                            ->orWhere('modelo', 'LIKE', "%{$termino}%")
                            ->orWhere('placa', 'LIKE', "%{$termino}%");
                })
                ->get();
    }
}
