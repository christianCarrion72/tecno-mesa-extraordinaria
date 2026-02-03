<?php
// app/Policies/VehiculoPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiculoPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Vehiculo $vehiculo)
    {
        return $user->id === $vehiculo->cliente_id;
    }

    public function update(User $user, Vehiculo $vehiculo)
    {
        return $user->id === $vehiculo->cliente_id;
    }

    public function delete(User $user, Vehiculo $vehiculo)
    {
        return $user->id === $vehiculo->cliente_id;
    }
}
