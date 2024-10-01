<?php

use App\Http\Controllers\API\Compro\ProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('compro')->group(function () {
    Route::get('program', [ProgramController::class, 'index']);
    Route::get('program/{slug}', [ProgramController::class, 'show']);
});
