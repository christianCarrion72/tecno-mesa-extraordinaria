<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $fillable = [
        'nombre',
        'foto',
    ];

    public function motores()
    {
        return $this->hasMany(Motor::class);
    }
    
}
