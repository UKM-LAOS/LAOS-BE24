<?php

use App\Http\Controllers\API\Compro\ArticleController;
use App\Http\Controllers\API\Compro\ProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('compro')->group(function () {
    // Program Route
    Route::get('program', [ProgramController::class, 'index']);
    Route::get('program/{slug}', [ProgramController::class, 'show']);

    Route::get('article', [ArticleController::class, 'index']);
    Route::get('article/unggulan', [ArticleController::class, 'isUnggulanArticle']);
    Route::get('article/{slug}', [ArticleController::class, 'show']);
});
