<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Modelo;

class ModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelos = [
            ['nombre' => 'Corolla', 'foto' => null],
            ['nombre' => 'Civic', 'foto' => null],
            ['nombre' => 'F-150', 'foto' => null],
            ['nombre' => 'Silverado', 'foto' => null],
            ['nombre' => 'Altima', 'foto' => null],
        ];

        foreach ($modelos as $modelo) {
            Modelo::create($modelo);
        }
    }
}
