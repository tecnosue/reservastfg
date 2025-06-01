<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zona; // Importamos el modelo Zona
use Inertia\Inertia; // Importamos Inertia

class ZonaController extends Controller
{
    // Método que mostrará el listado de zonas
    public function index()
    {
        //Pedimos al modelo todas las zonas de la BBDD y las guardamos en una variable
        $zonas = Zona::all();

        //Devolvemos la vista de Vue (Zonas/Index.vue) y le pasamos los datos
        return Inertia::render('Zonas/Index', [
            'zonas' => Zona::all(),
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva zona.
     */
    public function create()
    {
        // Este método muestra la página con el formulario.
        return Inertia::render('Zonas/Create');
    }

    /**
     * Guarda una nueva zona en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos que vienen del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // requerido, debe ser imagen, tipos permitidos, tamaño máx 2MB
        ]);

        // 2. Gestionar la subida de la imagen
        $imagePath = $request->file('imagen')->store('zonas', 'public');
        // Esto guarda la imagen en 'storage/app/public/zonas' y nos da la ruta.

        // 3. Crear la nueva zona en la base de datos
        Zona::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'imagen' => $imagePath, // Guardamos la ruta de la imagen
        ]);

        // 4. Redirigir al usuario al listado de zonas con un mensaje de éxito.
        return to_route('zonas.index')->with('message', 'Zona creada correctamente.');
    }
}
