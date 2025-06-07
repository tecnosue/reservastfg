<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Grupo;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run()
    {
        Grupo::create(['nombre' => 'Grupo A']);
        Grupo::create(['nombre' => 'Grupo B']);
    }
}

