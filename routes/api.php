<?php

use App\Http\Controllers\Timetables\DeleteTimetableController;
use App\Http\Controllers\Timetables\IndexTimetableController;
use App\Http\Controllers\Timetables\ShowTimetableController;
use App\Http\Controllers\Timetables\StoreTimetableController;
use App\Http\Controllers\Timetables\UpdateTimetableController;
use Illuminate\Support\Facades\Route;

// ... existing code ...

Route::prefix('timetables')->group(function () {
    Route::get('/', IndexTimetableController::class);
    Route::post('/', StoreTimetableController::class);
    Route::get('/{timetable}', ShowTimetableController::class);
    Route::put('/{timetable}', UpdateTimetableController::class);
    Route::delete('/{timetable}', DeleteTimetableController::class);
}); 