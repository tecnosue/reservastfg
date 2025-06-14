<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ReglaReservaController;
use App\Http\Controllers\GrupoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/pendiente', function () {
    return Inertia::render('Auth/PendienteActivacion');
})->name('pendiente');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Rutas de Zonas ---
// Ruta para que TODOS los usuarios autenticados vean el listado de zonas
Route::get('/zonas', [ZonaController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('zonas.index');

    


    

// Rutas de Zonas SÓLO para el administrador
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/zonas/create', [ZonaController::class, 'create'])->name('zonas.create');
    Route::post('/zonas', [ZonaController::class, 'store'])->name('zonas.store');
    Route::get('/zonas/{zona}', [ZonaController::class, 'edit'])->name('zonas.edit');
    Route::put('/zonas/{zona}', [ZonaController::class, 'update'])->name('zonas.update');
    Route::delete('/zonas/{zona}', [ZonaController::class, 'destroy'])->name('zonas.destroy');
    Route::get('/usuarios/pendientes', [DashboardController::class, 'usuariosPendientes'])->name('usuarios.pendientes');
    Route::put('/usuarios/{user}/activar', [DashboardController::class, 'activar'])->name('usuarios.activar');
    Route::get('/reglas-reserva', [ReglaReservaController::class, 'edit'])->name('reglas.edit');
    Route::put('/reglas-reserva', [ReglaReservaController::class, 'update'])->name('reglas.update');
   // Nuevas rutas para grupos
    Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
    Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
    Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy');


});


// --- Rutas de Reservas (para usuarios autenticados) ---
Route::middleware(['auth', 'verified', 'activo'])->group(function() {
    Route::get('/mis-reservas', [ReservaController::class, 'index'])->name('reservas.index');
    Route::get('/zonas/{zona}/reservar', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy');
    
});


// --- Rutas de Perfil (de Breeze) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';