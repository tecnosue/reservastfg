<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Zona;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Grupo;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard apropiado según el rol del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->rol === 'administrador') {
            return Inertia::render('DashboardAdmin', [
                'zonas' => Zona::all(),
                'usuariosPendientes' => User::where('activo', false)->get(), // usuarios pendientes

            ]);
        } else {
            return Inertia::render('DashboardUsuario', [
                'zonas' => $user->grupo ? $user->grupo->zonas : Zona::all(), // Zonas del grupo del usuario
                'usuario' => $user, // Información del usuario autenticado
                'grupo' => $user->grupo, // Información del grupo al que pertenece el usuario
            
            ]);
        }
    }

    public function usuariosPendientes()
    {
        $usuarios = User::where('activo', false)->get();
        $grupos = Grupo::all();

        return Inertia::render('Usuarios/Pendientes', [
            'usuarios' => $usuarios,
            'grupos' => $grupos, // Pasamos los grupos para que se puedan asignar
        ]);
    }

    public function activar(Request $request, User $user)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $user->activo = true;
        $user->grupo_id = $request->grupo_id;
        $user->save();

        return redirect()->back()->with('message', 'Usuario activado correctamente.');
    }
}
