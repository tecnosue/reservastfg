<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zona_id',
        'grupo_id', 
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
