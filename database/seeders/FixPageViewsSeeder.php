<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixPageViewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos antiguos con nombres inconsistentes
        // Puedes ejecutar esto manualmente si lo deseas:
        // php artisan db:seed --class=FixPageViewsSeeder
        
        // Opción 1: Eliminar TODOS los registros y empezar de nuevo
        // DB::table('page_views')->truncate();
        
        // Opción 2: Mantener los datos existentes
        // Los nuevos registros usarán los nombres consistentes del middleware actualizado
        
        $this->command->info('Page views seeder completed.');
    }
}
