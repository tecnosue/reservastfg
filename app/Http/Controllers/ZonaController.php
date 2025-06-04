<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zona; // Importamos el modelo Zona
use Inertia\Inertia; // Importamos Inertia
use Illuminate\Support\Facades\Storage; // Para manejar el almacenamiento de archivos

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
    /**
     * Muestra el formulario para editar una zona existente.
     */
    public function edit(Zona $zona) // Route Model Binding
    {
        return Inertia::render('Zonas/Edit', [
            'zona' => $zona,
        ]);
    }

    /**
     * Actualiza una zona existente en la base de datos.
     */
    public function update(Request $request, Zona $zona)
    {
        // 1. Validar los datos que vienen del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Opcional, pero debe ser imagen
        ]);
        // Actualizamos solo los campos de texto primero
        $zona->nombre = $validated['nombre'];
        $zona->descripcion = $validated['descripcion'];

        if ($request->hasFile('imagen')) {
            // Borrar la imagen antigua si existe
            if ($zona->imagen) {
                Storage::disk('public')->delete($zona->imagen);
            }
            // Subir y guardar la nueva imagen
            $zona->imagen = $request->file('imagen')->store('zonas', 'public');
        }
        // Si no se envía un nuevo archivo ($request->hasFile('imagen') es falso),
        // NO tocamos $zona->imagen, por lo que conservará su valor anterior.

        $zona->save(); // Guardamos los cambios

        // 4. Redirigir al usuario al listado de zonas con un mensaje de éxito.
        return to_route('zonas.index')->with('message', 'Zona actualizada correctamente.');
    }
    /**
     * Elimina una zona de la base de datos.
     */
    public function destroy(Zona $zona)
    {
        // 1. Borrar la imagen asociada del almacenamiento si existe
        if ($zona->imagen) {
            Storage::disk('public')->delete($zona->imagen); // Descomentar y asegurar que está
        }

        // 2. Eliminar la zona de la base de datos
        $zona->delete();

        // 3. Redirigir al usuario al listado de zonas con un mensaje de éxito.
        return to_route('zonas.index')->with('message', 'Zona eliminada correctamente.');
    }
}
