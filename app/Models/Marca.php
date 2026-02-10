<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'foto',
    ];

    public function motores()
    {
        return $this->hasMany(Motor::class);
    }


}
