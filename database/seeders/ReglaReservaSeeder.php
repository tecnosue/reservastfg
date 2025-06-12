<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReglaReserva;
use App\Models\Grupo;

class ReglaReservaSeeder extends Seeder
{
    public function run()
    {
        // Asumimos que tienes los grupos ya creados:
        $grupoA = Grupo::where('nombre', 'Grupo A')->first();
        $grupoB = Grupo::where('nombre', 'Grupo B')->first();

        // Si no existen, puedes crearlos aquí, pero normalmente ya los tienes por el GrupoSeeder

        if ($grupoA) {
            ReglaReserva::create([
                'grupo_id'       => $grupoA->id,
                'max_por_dia'    => 2,
                'max_por_semana' => 5,
                'max_por_mes'    => 10,
                'max_por_anio'   => 100,
            ]);
        }

        if ($grupoB) {
            ReglaReserva::create([
                'grupo_id'       => $grupoB->id,
                'max_por_dia'    => 1,
                'max_por_semana' => 3,
                'max_por_mes'    => 7,
                'max_por_anio'   => 50,
            ]);
        }
    }
}

