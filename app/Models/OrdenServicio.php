<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'orden_servicios';
    public $timestamps = false;

    protected $fillable = [
        'orden_trabajo_id',
        'servicio_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Relación con la orden de trabajo
     */
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    /**
     * Relación con el servicio
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    // =========================================================================
    // MÉTODOS DE UTILIDAD
    // =========================================================================

    /**
     * Calcular el subtotal manualmente
     */
    public function getSubtotalCalculadoAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Obtener información resumida del servicio en la orden
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'servicio' => $this->servicio->nombre,
            'descripcion' => $this->descripcion,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'subtotal' => $this->subtotal,
        ];
    }

    // Accessor para calcular el subtotal
    public function getSubtotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    // Accessors para asegurar que sean números
    public function getPrecioUnitarioAttribute($value)
    {
        return (float) $value;
    }
}
