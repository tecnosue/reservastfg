<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglaReserva extends Model
{
    protected $table = 'reglas_reserva';

    protected $fillable = [
        'max_por_dia',
        'max_por_semana',
        'max_por_mes',
        'max_por_anio',
    ];

    public $timestamps = false;
}
