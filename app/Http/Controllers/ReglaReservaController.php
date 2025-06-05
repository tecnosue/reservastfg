<?php

namespace App\Http\Controllers;

use App\Models\ReglaReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ReglaReservaController extends Controller
{
    //
    public function edit()
    {
        $reglas = ReglaReserva::first();    

        return Inertia::render('EditarReglasReserva', [
            'reglas' => $reglas
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_por_dia' => 'required|integer|min:0',
            'max_por_semana' => 'required|integer|min:0',
            'max_por_mes' => 'required|integer|min:0',
            'max_por_anio' => 'required|integer|min:0',
        ]);

        $regla = ReglaReserva::first();
        if ($regla) {
            $regla->update($request->only('max_por_dia', 'max_por_semana', 'max_por_mes', 'max_por_anio'));
        } else {
            ReglaReserva::create($request->only('max_por_dia', 'max_por_semana', 'max_por_mes', 'max_por_anio'));
        }


        return redirect()->route('reglas.edit')->with('message', 'Reglas actualizadas correctamente.');
    }
}
