<?php

namespace App\Http\Controllers;

use App\Models\ReglaReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Grupo;



class ReglaReservaController extends Controller
{
    //
    public function edit()
    {
        return Inertia::render('EditarReglasReserva', [
            'reglas' => ReglaReserva::first(),
            'grupos' => \App\Models\Grupo::all(), // 👈 importante
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'max_por_dia' => 'required|integer|min:0',
            'max_por_semana' => 'required|integer|min:0',
            'max_por_mes' => 'required|integer|min:0',
            'max_por_anio' => 'required|integer|min:0',
        ]);

        $regla = \App\Models\ReglaReserva::where('grupo_id', $request->grupo_id)->first();

        if ($regla) {
            $regla->update([
                'max_por_dia'    => $request->max_por_dia,
                'max_por_semana' => $request->max_por_semana,
                'max_por_mes'    => $request->max_por_mes,
                'max_por_anio'   => $request->max_por_anio,
            ]);
        } else {
            \App\Models\ReglaReserva::create([
                'grupo_id'       => $request->grupo_id, // 👈 ESTA LÍNEA ES IMPRESCINDIBLE
                'max_por_dia'    => $request->max_por_dia,
                'max_por_semana' => $request->max_por_semana,
                'max_por_mes'    => $request->max_por_mes,
                'max_por_anio'   => $request->max_por_anio,
            ]);
        }

        return redirect()->route('reglas.edit')->with('message', 'Reglas actualizadas correctamente.');
    }
}
