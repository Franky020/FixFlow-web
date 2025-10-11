<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/user', [ApiController::class, 'getUsers'])->name('user.list');
Route::post('/user', [ApiController::class, 'createUser'])->name('user.create');

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

Route::get('/dashboard', function () {
    return view('back.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
