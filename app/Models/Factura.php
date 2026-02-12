<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use SoftDeletes;

    protected $table = 'facturas';

    protected $fillable = [
        'descripcion',
        'estado',
        'fechaemision',
        'montototal',
        'nroautorizacion',
        'numerofactura',
        'pago_id',
    ];

    protected $casts = [
        'fechaemision' => 'date',
        'montototal' => 'float',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}

