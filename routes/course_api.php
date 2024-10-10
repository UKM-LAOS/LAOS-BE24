<?php

use App\Http\Controllers\API\Course\AuthController;
use App\Http\Controllers\Api\Course\CourseController;
use App\Http\Controllers\API\Course\MyCourseController;
use App\Http\Controllers\API\Course\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('course')->group(function () {
    Route::middleware('guest.api')->group(function () {
        // Auth Route
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);

        // Landing Page Route
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/search', [CourseController::class, 'search']);
        Route::get('/search/{slug}', [CourseController::class, 'show']);
    });

    Route::middleware('auth.api')->group(function () {
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::put('/auth/update-profile', [AuthController::class, 'updateProfile']);
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // My Course Route
        Route::get('/my-courses', [MyCourseController::class, 'index']);
        Route::get('/my-courses/{slug}', [MyCourseController::class, 'show']);

        // Transaction Route
        Route::get('/transactions', [TransactionController::class, 'index']);
    });
});
