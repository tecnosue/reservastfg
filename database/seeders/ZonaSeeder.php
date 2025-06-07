<?php

namespace Database\Seeders;

use App\Models\Zona;
use App\Models\Grupo;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    public function run()
    {
        $grupoA = \App\Models\Grupo::where('nombre', 'Grupo A')->first();
        $grupoB = \App\Models\Grupo::where('nombre', 'Grupo B')->first();

        Zona::create([
            'nombre' => 'Pista de Tenis',
            'descripcion' => 'Zona de pista de Tenis',
            'imagen' => 'zonas/pistaTenis.jpg',
            'grupo_id' => $grupoA->id,
        ]);

        Zona::create([
            'nombre' => 'Barbacoa',
            'descripcion' => 'Espacio para barbacoa',
            'imagen' => 'zonas/barbacoa.jpg',
            'grupo_id' => $grupoA->id,
        ]);

        Zona::create([
            'nombre' => 'Pista de Padel',
            'descripcion' => 'Zona de Pista de Padel',
            'imagen' => 'zonas/pistaPadel.jpg',
            'grupo_id' => $grupoB->id,
        ]);

        Zona::create([
            'nombre' => 'Pista de Fronton',
            'descripcion' => 'Zona de Pista de Fronton',
            'imagen' => 'zonas/Fronton.jpg',
            'grupo_id' => $grupoB->id,
        ]);
    }
}

