<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zona;
use App\Models\Grupo;

use App\Models\Reserva;
use App\Models\ReglaReserva;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // <-- 1. Importar Auth
use Carbon\Carbon;                   // <-- 2. Importar Carbon para manejar fechas/horas
use Illuminate\Support\Facades\Redirect; // <-- 3. Importar Redirect

class ReservaController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva reserva.
     *
     * @param Zona $zona
     * @return \Inertia\Response
     */
    public function create(Zona $zona)
    {
        $zona->load('disponibilidades');

        $reservas = Reserva::where('zona_id', $zona->id)
            ->where('fecha', '>=', now()->toDateString())
            ->where('estado', 'activa') // Solo reservas activas
            ->get(['fecha', 'hora_inicio']);
        $user = Auth::user();

        // 👇 Solo pasamos los grupos si el usuario es admin
        $grupos = $user->rol === 'administrador' ? \App\Models\Grupo::all() : [];
        return Inertia::render('Reservas/Create', [
            'zona' => $zona,
            'reservas' => $reservas,
            'disponibilidades' => $zona->disponibilidades,
            'grupos' => $grupos,
        ]);
    }

    /**
     * Guarda la nueva reserva en la base de datos.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDACIÓN DE DATOS BÁSICOS
        $request->validate([
            'zona_id' => 'required|exists:zonas,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            // El grupo solo es obligatorio si eres administrador
            //'grupo_id' => $user->rol === 'administrador' ? 'required|exists:grupos,id' : '',
        ]);

        // COMPROBACIÓN DE DOBLE RESERVA
        // Verificamos si alguien ha reservado este hueco mientras lo elegíamos.
        $reservaExistente = Reserva::where('zona_id', $request->zona_id)
            ->where('fecha', $request->fecha)
            ->where('hora_inicio', $request->hora_inicio)
            ->where('estado', 'activa') //Solo bloqueamos si la reserva está activa
            ->exists(); // ->exists() devuelve true si encuentra algo

        if ($reservaExistente) {
            // Si el hueco ya está cogido, volvemos atrás con un error específico.
            return Redirect::back()->with('message', 'Este tramo horario ya no está disponible. Por favor, selecciona otro.');
        }

        // 👇 Aquí se obtiene la zona con su grupo
        $zona = \App\Models\Zona::findOrFail($request->zona_id);
        
        if (!$zona->grupo_id) {
        return back()->with('error', 'La zona seleccionada no tiene grupo asignado.');
        }

       





        // OBTENER LAS REGLAS DEL GRUPO
        $regla = ReglaReserva::where('grupo_id', $zona->grupo_id)->first();

        if (!$regla) {
            return back()->with('error', 'Este grupo no tiene reglas definidas.');
        }

        // CONTAR RESERVAS DEL USUARIO EN ESTE GRUPO
         $fecha = $request->fecha;


        $reservasDia = Reserva::where('user_id', $user->id)
            ->where('grupo_id', $zona->grupo_id)
            ->whereDate('fecha', $fecha)
            ->where('estado', 'activa')
            ->count();

        $reservasSemana = Reserva::where('user_id', $user->id)
            ->where('grupo_id', $zona->grupo_id)
            ->whereBetween('fecha', [
                Carbon::parse($fecha)->startOfWeek(),
                Carbon::parse($fecha)->endOfWeek()

            ])
            ->where('estado', 'activa')
            ->count();

        $reservasMes = Reserva::where('user_id', $user->id)
            ->where('grupo_id', $zona->grupo_id)
            ->whereMonth('fecha', Carbon::parse($fecha)->month)
            ->whereYear('fecha', Carbon::parse($fecha)->year)
            ->where('estado', 'activa')
            ->count();

        $reservasAnio = Reserva::where('user_id', $user->id)
            ->where('grupo_id', $zona->grupo_id)
            ->whereYear('fecha', Carbon::parse($fecha)->year)
            ->where('estado', 'activa')
            ->count();

        // VALIDACIONES CONTRA LAS REGLAS
        if ($reservasDia >= $regla->max_por_dia) {
            return back()->with('error', 'Has superado el límite de reservas por día para esta zona.');
        }

        if ($reservasSemana >= $regla->max_por_semana) {
            return back()->with('error', 'Has superado el límite de reservas por semana para esta zona.');
        }

        if ($reservasMes >= $regla->max_por_mes) {
            return back()->with('error', 'Has superado el límite de reservas por mes para esta zona.');
        }

        if ($reservasAnio >= $regla->max_por_anio) {
            return back()->with('error', 'Has superado el límite de reservas por año para esta zona.');
        }

        // CREACIÓN DE LA RESERVA
        // Si todo es correcto, creamos la reserva en la base de datos.
        Reserva::create([
            'user_id' => $user->id, // El ID del usuario que está haciendo la reserva
            'zona_id' => $zona->id, // El ID de la zona que se está reservando
            'grupo_id' => $zona->grupo_id, // El ID del grupo al que pertenece la zona
            'fecha' => $fecha,
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
        // COMPROBACIÓN DE SEGURIDAD (MUY IMPORTANTE)
        // Nos aseguramos de que el usuario que intenta cancelar la reserva
        // es el mismo que la creó.
        if (Auth::id() !== $reserva->user_id) {
            abort(403, 'Acción no autorizada.');
        }

        // ACTUALIZAR EL ESTADO
        // Cambiamos el estado de la reserva a 'cancelada'.
        $reserva->update(['estado' => 'cancelada']);

        // REDIRECCIÓN CON ÉXITO
        // Devolvemos al usuario a la lista de sus reservas con un mensaje.
        return to_route('reservas.index')->with('message', 'La reserva ha sido cancelada.');
    }
}
