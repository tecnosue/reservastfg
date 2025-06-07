<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zona;
use App\Models\ZonaDisponibilidad;

class ZonaDisponibilidadSeeder extends Seeder
{
    public function run()
    {
        $dias = ['lunes', 'miércoles',  'viernes', 'sábado','domingo']; // Días de la semana para la disponibilidad
        $horas = [
         
            ['18:00:00', '19:00:00'],
            ['19:00:00', '20:00:00'],
            ['20:00:00', '21:00:00'],
        ];

        $zonas = Zona::all();

        foreach ($zonas as $zona) {
            foreach ($dias as $dia) {
                foreach ($horas as $horario) {
                    ZonaDisponibilidad::create([
                        'zona_id' => $zona->id,
                        'dia_semana' => $dia,
                        'hora_inicio' => $horario[0],
                        'hora_fin' => $horario[1],
                    ]);
                }
            }
        }
    }
}
