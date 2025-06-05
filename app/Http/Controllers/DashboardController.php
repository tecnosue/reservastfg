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

    public function usuariosPendientes()
    {
        $usuarios = User::where('activo', false)->get();

        return Inertia::render('Usuarios/Pendientes', [
            'usuarios' => $usuarios
        ]);
    }

    public function activar($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->activo = true;
        $usuario->save();
    
        return redirect()->back()->with('message', 'Usuario activado correctamente.');
    }

}