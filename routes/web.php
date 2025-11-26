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
use App\Http\Controllers\InicioDController;
use Illuminate\Support\Facades\Http;  // ✅ AGREGADO

Route::get('/roles-stats', [InicioDController::class, 'rolesStats']);
// ----------------------------------------------------------------------
// Rutas de Usuarios (Users Management)
// ----------------------------------------------------------------------
Route::get('/user', [ApiController::class, 'getUsers'])->name('user.list');
Route::get('/user-data-proxy', [LoginApiController::class, 'getUserData']);

Route::post('/user', [ApiController::class, 'createUser'])->name('user.create');
Route::post('/feedback/submit', [FeedbackController::class, 'store'])->name('feedback.store');

Route::put('/user/{id}', [ApiController::class, 'updateUser'])->name('user.update'); 
Route::delete('/user/{id}', [ApiController::class, 'deleteUser'])->name('user.delete');


// ----------------------------------------------------------------------
// Vistas
// ----------------------------------------------------------------------
Route::view('/login', 'publico.login')->name('login');
Route::get('/dashboardsU', function () {
    return view('inicioD.index');
})->name('dashboards');


/* ✅ PROXY PARA LA GRÁFICA (EVITA CORS) */
Route::get('/companies-stats', function () {
    $response = Http::get('https://fixflow-endpoints.onrender.com/api/companies/stats/');
    return $response->json();
});
Route::get('/tickets-status-stats', [InicioDController::class, 'ticketsStatusStats']);

Route::post('/api-login', [LoginApiController::class, 'login'])->name('login.api');
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');


// ----------------------------------------------------------------------
// Rutas de Tickets
// ----------------------------------------------------------------------
Route::get('/tickets', [ApiController::class, 'getTickets'])->name('tickets.index');
Route::post('/tickets', [ApiController::class, 'createTicket'])->name('tickets.create');
Route::put('/tickets/{id}', [ApiController::class, 'updateTicket'])->name('tickets.update');
Route::delete('/tickets/{id}', [ApiController::class, 'deleteTicket'])->name('tickets.delete'); 
Route::get('/tickets/{id}', [ApiController::class, 'getTicketDetailsForView'])->name('tickets.details');


// ----------------------------------------------------------------------
// Rutas de Spare Parts
// ----------------------------------------------------------------------
Route::get('/spare-parts', [ApiController::class, 'getSpareParts'])->name('spare_parts.index');
Route::post('/spare-parts', [ApiController::class, 'createSparePart'])->name('spare_parts.store');
Route::get('/spare-parts/{id}/details', [ApiController::class, 'getSparePartDetailsForView'])->name('spare_parts.details');
Route::put('/spare-parts/{id}', [ApiController::class, 'updateSparePart'])->name('spare_parts.update');
Route::delete('/spare-parts/{id}', [ApiController::class, 'deleteSparePart'])->name('spare_parts.destroy');


// ----------------------------------------------------------------------
// Reportes
// ----------------------------------------------------------------------
Route::get('/reports/{id}/export-pdf', [ReportController::class, 'exportPdf'])
    ->name('reports.export.pdf');

Route::controller(ReportController::class)->group(function () {
    Route::get('/reports', 'index')->name('reports.index');
    Route::post('/reports', 'create')->name('report.create'); 

    Route::post('/reports/{id}/message', [ReportController::class, 'storeMessage'])
        ->name('reports.message.store');

    Route::get('/reports/{id}', 'show')->name('reports.show'); 
    Route::delete('/reports/{id}', 'delete')->name('reports.delete'); 
});


// ----------------------------------------------------------------------
// Compañías
// ----------------------------------------------------------------------
Route::controller(CompanyController::class)->group(function () {

    Route::get('/companies', 'index')->name('companies.index'); 
    Route::post('/companies', 'create')->name('company.create'); 

    Route::get('/companies/{id}/edit', 'edit')->name('company.edit'); 
    Route::put('/companies/{id}', 'update')->name('company.update'); 

    Route::delete('/companies/{id}', 'destroy')->name('company.destroy'); 
});


// ----------------------------------------------------------------------
// Feedback
// ----------------------------------------------------------------------
Route::get('/feedback/create', [FeedbackController::class, 'create'])
    ->name('feedback.create');


// ----------------------------------------------------------------------
// Localizaciones
// ----------------------------------------------------------------------
Route::controller(LocationController::class)->group(function () {

    Route::get('/locations', 'index')->name('locations.index'); 
    Route::post('/locations', 'store')->name('locations.store');
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

Route::get('/feed', function () {
    return view('publico.feed');
})->name('feed');

Route::get('/nosotros', function () {
    return view('publico.about');
})->name('about');

Route::get('/contacto', function () {
    return view('publico.consultar');
})->name('consultar');


// ----------------------------------------------------------------------
// Rutas protegidas / Dashboard
// ----------------------------------------------------------------------
Route::get('/dashboard', function () {
    return view('back.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

});

require __DIR__.'/auth.php';
