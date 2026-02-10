<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Toyota', 'foto' => null],
            ['nombre' => 'Honda', 'foto' => null],
            ['nombre' => 'Ford', 'foto' => null],
            ['nombre' => 'Chevrolet', 'foto' => null],
            ['nombre' => 'Nissan', 'foto' => null],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }
    }
}
