<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoDetalle extends Model
{
    use HasFactory;

    protected $table = 'pago_detalles';
    public $timestamps = false;

    protected $fillable = [
        'pago_id',
        'numero_cuota',
        'monto',
        'metodo_pago',
        'numero_comprobante',
        'banco',
        'referencia',
        'fecha_pago',
        'hora_pago',
        'recibido_por',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Relación con el pago
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    /**
     * Relación con el usuario que recibió el pago
     */
    public function recibidoPor()
    {
        return $this->belongsTo(User::class, 'recibido_por');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope para pagos en efectivo
     */
    public function scopeEfectivo($query)
    {
        return $query->where('metodo_pago', 'efectivo');
    }

    /**
     * Scope para pagos QR
     */
    public function scopeQr($query)
    {
        return $query->where('metodo_pago', 'qr');
    }

    /**
     * Scope para pagos de una fecha específica
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->format('Y-m-d');
        return $query->where('fecha_pago', $fecha);
    }

    /**
     * Scope para pagos del mes actual
     */
    public function scopeDelMesActual($query)
    {
        return $query->whereMonth('fecha_pago', now()->month)
                    ->whereYear('fecha_pago', now()->year);
    }

    // =========================================================================
    // MÉTODOS DE UTILIDAD
    // =========================================================================

    /**
     * Verificar si el pago es en efectivo
     */
    public function esEfectivo()
    {
        return $this->metodo_pago === 'efectivo';
    }

    /**
     * Verificar si el pago es QR
     */
    public function esQr()
    {
        return $this->metodo_pago === 'qr';
    }

    /**
     * Obtener el texto legible del método de pago
     */
    public function getMetodoPagoTextoAttribute()
    {
        return $this->esEfectivo() ? 'Efectivo' : 'QR';
    }

    /**
     * Obtener información resumida del detalle
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'numero_cuota' => $this->numero_cuota,
            'monto' => $this->monto,
            'metodo_pago' => $this->metodo_pago_texto,
            'fecha_pago' => $this->fecha_pago->format('d/m/Y'),
            'hora_pago' => $this->hora_pago,
            'recibido_por' => $this->recibidoPor->nombre ?? 'N/A',
        ];
    }

    /**
     * Obtener los métodos de pago disponibles
     */
    public static function getMetodosPagoDisponibles()
    {
        return [
            'efectivo' => 'Efectivo',
            'qr' => 'QR',
        ];
    }

    /**
     * Generar número de comprobante automático
     */
    public static function generarNumeroComprobante($metodoPago)
    {
        $prefix = $metodoPago === 'efectivo' ? 'EF' : 'QR';
        $fecha = now()->format('Ymd');
        $secuencia = self::where('metodo_pago', $metodoPago)
                        ->whereDate('created_at', now())
                        ->count() + 1;

        return $prefix . '-' . $fecha . '-' . str_pad($secuencia, 4, '0', STR_PAD_LEFT);
    }
}
