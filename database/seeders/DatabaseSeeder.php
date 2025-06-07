<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

            // Aquí podrías llamar a otros seeders si los tuvieras
        ]);
        
    }
}
