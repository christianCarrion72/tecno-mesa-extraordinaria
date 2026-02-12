<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoServicio extends Model
{
    protected $table = 'orden_trabajo_servicios';

    protected $fillable = [
        'orden_trabajo_id',
        'servicio_id',
        'cantidad',
        'precio',
        'subtotal',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
    
}
