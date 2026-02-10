<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propietario = Rol::create(['nombre' => 'Propietario']);
        $mecanico = Rol::create(['nombre' => 'Mecanico']);

        //MARCAS OK
        Permiso::create([
            'nombre' => 'marca.listar',
            'descripcion' => 'Ver lista de marcas',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'marca.crear',
            'descripcion' => 'Crear marcas',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'marca.editar',
            'descripcion' => 'Editar marcas',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'marca.eliminar',
            'descripcion' => 'Eliminar marcas',
        ])->roles()->attach([$propietario->id]);

        //MODELOS
        Permiso::create([
            'nombre' => 'modelo.listar',
            'descripcion' => 'Listar modelos',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'modelo.crear',
            'descripcion' => 'Crear modelos',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'modelo.editar',
            'descripcion' => 'Editar modelos',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'modelo.eliminar',
            'descripcion' => 'Eliminar modelos',
        ])->roles()->attach([$propietario->id]);

        //USUARIOS
        Permiso::create([
            'nombre' => 'usuario.listar',
            'descripcion' => 'Listar usuarios',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'usuario.crear',
            'descripcion' => 'Crear usuarios',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'usuario.editar',
            'descripcion' => 'Editar usuarios',
        ])->roles()->attach([$propietario->id]);

        Permiso::create([
            'nombre' => 'usuario.eliminar',
            'descripcion' => 'Eliminar usuarios',
        ])->roles()->attach([$propietario->id]);

        //Clientes
        Permiso::create([
            'nombre' => 'cliente.listar',
            'descripcion' => 'Listar clientes',
        ])->roles()->attach([$propietario->id, $mecanico->id]);
        Permiso::create([
            'nombre' => 'cliente.crear',
            'descripcion' => 'Crear clientes',
        ])->roles()->attach([$propietario->id, $mecanico->id]);
        Permiso::create([
            'nombre' => 'cliente.editar',
            'descripcion' => 'Editar clientes',
        ])->roles()->attach([$propietario->id, $mecanico->id]);
        Permiso::create([
            'nombre' => 'cliente.eliminar',
            'descripcion' => 'Eliminar clientes',
        ])->roles()->attach([$propietario->id]);    

        //Motores
        Permiso::create([
            'nombre' => 'motor.listar',
            'descripcion' => 'Listar motores',
        ])->roles()->attach([$propietario->id, $mecanico->id]);
        Permiso::create([
            'nombre' => 'motor.crear',
            'descripcion' => 'Crear motores',
        ])->roles()->attach([$propietario->id, $mecanico->id]);     
        Permiso::create([
            'nombre' => 'motor.editar',
            'descripcion' => 'Editar motores',
        ])->roles()->attach([$propietario->id, $mecanico->id]);     
        Permiso::create([
            'nombre' => 'motor.eliminar',
            'descripcion' => 'Eliminar motores',
        ])->roles()->attach([$propietario->id]);    

        //Partes
        Permiso::create([
            'nombre' => 'parte.listar',
            'descripcion' => 'Listar partes',
        ])->roles()->attach([$propietario->id, $mecanico->id]);
        Permiso::create([
            'nombre' => 'parte.crear',
            'descripcion' => 'Crear partes',
        ])->roles()->attach([$propietario->id, $mecanico->id]);     
        Permiso::create([
            'nombre' => 'parte.editar',
            'descripcion' => 'Editar partes',
        ])->roles()->attach([$propietario->id, $mecanico->id]);     
        Permiso::create([
            'nombre' => 'parte.eliminar',
            'descripcion' => 'Eliminar partes',
        ])->roles()->attach([$propietario->id]);  
    }
}
