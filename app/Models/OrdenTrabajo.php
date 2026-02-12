<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTrabajo extends Model
{
    use SoftDeletes;

    protected $table = 'orden_trabajos';

    protected $fillable = [
        'fechainicio',
        'fechafin',
        'descripcion',
        'total',
        'estado',
        'cliente_id',
        'usuario_id',
        'motor_id',
    ];

    // Casts
    protected $casts = [
        'fechainicio' => 'date',
        'fechafin' => 'date',
        'total' => 'float',
    ];

    // Estados del flujo de trabajo
    const ESTADO_PENDIENTE  = 'pendiente';
    const ESTADO_APROBADO   = 'aprobado';
    const ESTADO_PROCESO    = 'proceso';
    const ESTADO_TERMINADO  = 'terminado';

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }
    public function incidencias()
    {
    return $this->hasMany(Incidencia::class);
    }
    
    public function servicios()
    {
    return $this->belongsToMany(Servicio::class, 'orden_trabajo_servicios')
                ->withPivot(['id', 'cantidad', 'precio', 'subtotal'])
                ->withTimestamps();
    }

    public function planPago()
    {
        return $this->hasOne(PlanPago::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de estado
    |--------------------------------------------------------------------------
    */

    public function isPendiente()  { return $this->estado === self::ESTADO_PENDIENTE; }
    public function isAprobado()   { return $this->estado === self::ESTADO_APROBADO; }
    public function isProceso()    { return $this->estado === self::ESTADO_PROCESO; }
    public function isTerminado()  { return $this->estado === self::ESTADO_TERMINADO; }

    public function finalizar()
    {
        $this->estado = self::ESTADO_TERMINADO;
        $this->save();
    }
}
