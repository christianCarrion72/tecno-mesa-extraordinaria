<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UserSeeder::class,
            MarcaSeeder::class,
            ModeloSeeder::class,
            MotorSeeder::class,
            ParteSeeder::class,
            ClienteSeeder::class,
            ServicioSeeder::class,
            OrdenTrabajoSeeder::class,
            PagoSeeder::class,
            //VehiculoSeeder::class,
        ]);
    }
}
