<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parte;

class ParteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partes = [
            ['nombre' => 'Pistón', 'motor_id' => 1, 'foto' => null],
            ['nombre' => 'Biela', 'motor_id' => 1, 'foto' => null],
            ['nombre' => 'Árbol de levas', 'motor_id' => 2, 'foto' => null],
            ['nombre' => 'Válvula de escape', 'motor_id' => 3, 'foto' => null],
            ['nombre' => 'Culata', 'motor_id' => 4, 'foto' => null],
        ];

        foreach ($partes as $parte) {
            Parte::create($parte);
        }
    }
}
