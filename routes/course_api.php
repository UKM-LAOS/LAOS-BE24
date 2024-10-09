<?php

use App\Http\Controllers\Api\Course\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('course')->group(function () {
    Route::middleware('guest')->group(function () {
        // Course Route
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/search', [CourseController::class, 'search']);
        Route::get('/search/{slug}', [CourseController::class, 'show']);
    });
});
