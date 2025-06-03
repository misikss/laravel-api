<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Activities\DeleteActivityController;
use App\Http\Controllers\Activities\IndexActivityController;
use App\Http\Controllers\Activities\ShowActivityController;
use App\Http\Controllers\Activities\StoreActivityController;
use App\Http\Controllers\Activities\UpdateActivityController;
use App\Http\Controllers\Timetables\DeleteTimetableController;
use App\Http\Controllers\Timetables\IndexTimetableController;
use App\Http\Controllers\Timetables\ShowTimetableController;
use App\Http\Controllers\Timetables\StoreTimetableController;
use App\Http\Controllers\Timetables\UpdateTimetableController;

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    // Rutas públicas
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout']);
        Route::get('/user', [UserController::class, 'user']);
    });
});

// Rutas de Timetables
Route::middleware('auth:sanctum')->prefix('timetables')->group(function () {
    Route::get('/', IndexTimetableController::class);
    Route::post('/', StoreTimetableController::class);
    Route::get('/{timetable}', ShowTimetableController::class);
    Route::put('/{timetable}', UpdateTimetableController::class);
    Route::delete('/{timetable}', DeleteTimetableController::class);
});

// Rutas de Activities
Route::middleware('auth:sanctum')->prefix('activities')->group(function () {
    Route::get('/', IndexActivityController::class);
    Route::post('/', StoreActivityController::class);
    Route::get('/{activity}', ShowActivityController::class);
    Route::put('/{activity}', UpdateActivityController::class);
    Route::delete('/{activity}', DeleteActivityController::class);
}); 