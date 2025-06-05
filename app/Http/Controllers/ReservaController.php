<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zona;
use App\Models\Reserva;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // <-- 1. Importar Auth
use Carbon\Carbon;                   // <-- 2. Importar Carbon para manejar fechas/horas
use Illuminate\Support\Facades\Redirect; // <-- 3. Importar Redirect

class ReservaController extends Controller
{
    public function create(Zona $zona)
    {
        $reservas = Reserva::where('zona_id', $zona->id)
                           ->where('fecha', '>=', now()->toDateString())
                           ->get(['fecha', 'hora_inicio']);

        return Inertia::render('Reservas/Create', [
            'zona' => $zona,
            'reservas' => $reservas,
        ]);
    }

    /**
     * Guarda la nueva reserva en la base de datos.
     */
    public function store(Request $request)
    {
        // VALIDACIÓN DE DATOS
        // Nos aseguramos de que los datos que llegan son los que esperamos.
        $request->validate([
            'zona_id' => 'required|exists:zonas,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
        ]);

        // COMPROBACIÓN DE DOBLE RESERVA
        // Verificamos si alguien ha reservado este hueco mientras lo elegíamos.
        $reservaExistente = Reserva::where('zona_id', $request->zona_id)
                                    ->where('fecha', $request->fecha)
                                    ->where('hora_inicio', $request->hora_inicio)
                                    ->exists(); // ->exists() devuelve true si encuentra algo

        if ($reservaExistente) {
            // Si el hueco ya está cogido, volvemos atrás con un error específico.
            return Redirect::back()->withErrors(['hora_inicio' => 'Este tramo horario ya no está disponible. Por favor, selecciona otro.']);
        }

        // CREACIÓN DE LA RESERVA
        // Si todo es correcto, creamos la reserva en la base de datos.
        Reserva::create([
            'user_id' => Auth::id(), // El ID del usuario que está haciendo la reserva
            'zona_id' => $request->zona_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            // Calculamos la hora de fin sumando 1 hora a la de inicio
            'hora_fin' => Carbon::parse($request->hora_inicio)->addHour()->toTimeString(),
            'estado' => 'activa', // Definimos el estado inicial de la reserva
        ]);

        // REDIRECCIÓN CON ÉXITO
        // Redirigimos al usuario a su panel principal con un mensaje de éxito.
        return to_route('reservas.index')->with('message', '¡Tu reserva ha sido confirmada con éxito!');
    }

    /**
    * Muestra un listado de las reservas del usuario autenticado.
    */
    public function index()
    {
        $reservas = Reserva::where('user_id', Auth::id()) // Busca solo las del usuario logueado
                            ->with('zona')                 // Carga también la información de la zona asociada
                            ->latest()                     // Ordena de la más nueva a la más antigua
                            ->paginate(10);                // Pagina los resultados de 10 en 10

        return Inertia::render('Reservas/Index', [
            'reservas' => $reservas,
        ]);
    }

    /**
    * Cancela una reserva existente.
    */
    public function destroy(Reserva $reserva)
    {
        // 1. COMPROBACIÓN DE SEGURIDAD (MUY IMPORTANTE)
        // Nos aseguramos de que el usuario que intenta cancelar la reserva
        // es el mismo que la creó.
        if (Auth::id() !== $reserva->user_id) {
            abort(403, 'Acción no autorizada.');
        }

        // 2. ACTUALIZAR EL ESTADO
        // Cambiamos el estado de la reserva a 'cancelada'.
        $reserva->update(['estado' => 'cancelada']);

        // 3. REDIRECCIÓN CON ÉXITO
        // Devolvemos al usuario a la lista de sus reservas con un mensaje.
        return to_route('reservas.index')->with('message', 'La reserva ha sido cancelada.');
    }
}