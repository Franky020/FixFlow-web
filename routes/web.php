<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginApiController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;


// Rutas de Usuarios (Users Management)
// Listado (GET /user)
Route::get('/user', [ApiController::class, 'getUsers'])->name('user.list');
Route::get('/user-data-proxy', [LoginApiController::class, 'getUserData']);
// Creación (POST /user)
Route::post('/user', [ApiController::class, 'createUser'])->name('user.create');
Route::post('/feedback/submit', [FeedbackController::class, 'store'])->name('feedback.store');
// Actualización (PUT /user/{id})
// La URL para actualizar un recurso singular es más clara: /user/3
Route::put('/user/{id}', [ApiController::class, 'updateUser'])->name('user.update'); 

// Eliminación (DELETE /user/{id})
// La URL para eliminar un recurso singular es más clara: /user/3
Route::delete('/user/{id}', [ApiController::class, 'deleteUser'])->name('user.delete');
// ----------------------------------------------------------------------


// Vistas
Route::view('/login', 'publico.login')->name('login');
Route::view('/dashboardsU', 'inicioD.index')->name('dashboards');
Route::post('/api-login', [LoginApiController::class, 'login'])->name('login.api');
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

// Rutas de Tickets (Tickets Management)
// ----------------------------------------------------------------------
// Muestra el listado de tickets y el formulario de creación.
// Ruta para mostrar el listado de tickets y el formulario (GET)
Route::get('/tickets', [ApiController::class, 'getTickets'])->name('tickets.index');

// RUTA FALTANTE: Ruta para manejar la creación de un nuevo ticket (POST)
Route::post('/tickets', [ApiController::class, 'createTicket'])->name('tickets.create');

// ... el resto de tus rutas ...
Route::put('/tickets/{id}', [ApiController::class, 'updateTicket'])->name('tickets.update');
Route::delete('/tickets/{id}', [ApiController::class, 'deleteTicket'])->name('tickets.delete'); 
// También revisa esta, si la estás usando
Route::get('/tickets/{id}', [ApiController::class, 'getTicketDetailsForView'])->name('tickets.details');
// ----------------------------------------------------------------------
// Rutas de Spare Parts
// ----------------------------------------------------------------------
Route::get('/spare-parts', [ApiController::class, 'getSpareParts'])->name('spare_parts.index');

// Procesar el formulario de creación de una nueva pieza
Route::post('/spare-parts', [ApiController::class, 'createSparePart'])->name('spare_parts.store');

// Muestra la vista de detalles y edición de una pieza específica
Route::get('/spare-parts/{id}/details', [ApiController::class, 'getSparePartDetailsForView'])->name('spare_parts.details');

// Procesar la actualización de una pieza específica
Route::put('/spare-parts/{id}', [ApiController::class, 'updateSparePart'])->name('spare_parts.update');

// Procesar la eliminación de una pieza específica
Route::delete('/spare-parts/{id}', [ApiController::class, 'deleteSparePart'])->name('spare_parts.destroy');

Route::get('/reports/{id}/export-pdf', [ReportController::class, 'exportPdf'])
    ->name('reports.export.pdf');

    Route::controller(ReportController::class)->group(function () {
    // Listado y Formulario de Creación
    Route::get('/reports', 'index')->name('reports.index');
    Route::post('/reports', 'create')->name('report.create'); 
   Route::post('/reports/{id}/message', [ReportController::class, 'storeMessage'])
    ->name('reports.message.store');

    
    // Rutas AGREGADAS: Show y Delete
    Route::get('/reports/{id}', 'show')->name('reports.show'); 
    Route::delete('/reports/{id}', 'delete')->name('reports.delete'); 
});

// ----------------------------------------------------------------------
// Rutas de Compañías (Formulario Único Creación/Edición)
// ----------------------------------------------------------------------
Route::controller(CompanyController::class)->group(function () {
    
    // R (Read - Listado y Formulario Único)
    Route::get('/companies', 'index')->name('companies.index'); 
    
    // C (Create)
    Route::post('/companies', 'create')->name('company.create'); 
    
    // U (Update - Redirecciona al index con el ID para cargar el formulario)
    // NOTA: Esta ruta DEBE ser GET para que el enlace del botón funcione
    Route::get('/companies/{id}/edit', 'edit')->name('company.edit'); 
    
    // U (Update - Envío de Formulario)
    Route::put('/companies/{id}', 'update')->name('company.update'); 
    
    // D (Delete)
    Route::delete('/companies/{id}', 'destroy')->name('company.destroy'); 
});

Route::get('/feedback/create', [FeedbackController::class, 'create'])
    ->name('feedback.create');

// ----------------------------------------------------------------------
// Rutas de Localizaciones
// ----------------------------------------------------------------------
Route::controller(LocationController::class)->group(function () {
    // CRUD Básico: C(reate) y R(ead) - Listado y Formulario
    Route::get('/locations', 'index')->name('locations.index'); 
    Route::post('/locations', 'store')->name('locations.store');

    // CRUD Básico: U(pdate) y D(elete)
    Route::get('/locations/{id}/edit', 'edit')->name('location.edit'); 
    Route::put('/locations/{id}', 'update')->name('location.update'); 
    Route::delete('/locations/{id}', 'destroy')->name('location.destroy'); 
});
// ----------------------------------------------------------------------
// Rutas Públicas
// ----------------------------------------------------------------------
Route::get('/', function () {
    return view('publico.home');
})->name('home');

// Ruta para la página del Feed
Route::get('/feed', function () {
    return view('publico.feed');
})->name('feed');

// Ruta para la página "Acerca de Nosotros"
Route::get('/nosotros', function () {
    return view('publico.about');
})->name('about');

Route::get('/contacto', function () {
    return view('publico.consultar');
})->name('consultar');

// ----------------------------------------------------------------------
// Rutas Autenticadas (Dashboard y Perfil)
// ----------------------------------------------------------------------
Route::get('/dashboard', function () {
    return view('back.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



// ----------------------------------------------------------------------
// Rutas de Reportes
// ----------------------------------------------------------------------


Route::middleware('auth')->group(function () {
    // ... otras rutas de perfil

    // RUTA DE CIERRE DE SESIÓN
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

require __DIR__.'/auth.php';