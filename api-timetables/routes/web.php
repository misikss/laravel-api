<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para la documentación de la API
Route::get('/docs', function () {
    return view('scribe.index');
});
