<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motor extends Model
{
    use SoftDeletes;

    protected $table = 'motores';

    protected $fillable = [
        'numero_serie',
        'anio',
        'descripcion',
        'marca_id',
        'modelo_id',
        'foto',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function partes()
    {
        return $this->hasMany(Parte::class);
    }
}
