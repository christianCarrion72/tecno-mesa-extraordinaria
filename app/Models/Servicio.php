<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo',
    ];

    // Muchas órdenes pueden usar muchos servicios (pivot)
    public function ordenesTrabajo()
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'orden_trabajo_servicios')
                    ->withPivot(['cantidad', 'precio', 'subtotal'])
                    ->withTimestamps();
    }
}
