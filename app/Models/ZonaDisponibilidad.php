<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonaDisponibilidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'zona_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
//Este modelo representa la disponibilidad de una zona en un día específico, incluyendo el horario de inicio y fin.