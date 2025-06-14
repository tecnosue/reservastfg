<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class, 
            GrupoSeeder::class,
            ZonaSeeder::class,
            ZonaDisponibilidadSeeder::class,
            ReglaReservaSeeder::class, 

            // Aquí podremos llamar a otros seeders 
        ]);
        
    }
}
