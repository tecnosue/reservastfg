<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Zona;
use App\Models\User;
use Inertia\Inertia;

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
                'zonas' => Zona::all(),
            ]);
        }
    }

    /**
     * Activa un usuario específico (nueva funcionalidad)
     */
    public function activarUsuario(User $user)
    {
        $user->update(['activo' => true]);

        return to_route('dashboard')->with('message', 'Usuario activado correctamente.');
    }
}