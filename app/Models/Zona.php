<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
    public function disponibilidades()
    {
        return $this->hasMany(ZonaDisponibilidad::class);
    }
    public function grupo() {
        return $this->belongsTo(Grupo::class);
    }
}