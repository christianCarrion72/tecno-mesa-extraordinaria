<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parte extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'motor_id',
        'foto',
    ];

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }
}
