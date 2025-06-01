<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Zona;
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
            return Inertia::render('DashboardAdmin');
        } else {
            return Inertia::render('DashboardUsuario', [
                'zonas' => Zona::all(),
            ]);
        }
    }
}