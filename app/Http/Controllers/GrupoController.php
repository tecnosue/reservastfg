<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GrupoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Grupos', [
            'grupos' => Grupo::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:grupos',
        ]);

        Grupo::create($request->only('nombre'));

        return redirect()->back()->with('success', 'Grupo creado correctamente');
    }

    public function destroy(Grupo $grupo)
    {
        // Verificar que no hay usuarios asociados
        if ($grupo->usuarios()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un grupo con usuarios asignados');
        }

        $grupo->delete();
        return redirect()->back()->with('success', 'Grupo eliminado correctamente');
    }
}