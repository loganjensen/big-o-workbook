<?php

use App\Http\Controllers\BigO\BigOQuizController;
use Illuminate\Support\Facades\Route;

Route::prefix('big-o')->name('api.big-o.')->group(function () {
    Route::get('/{slug}/quiz', [BigOQuizController::class, 'show'])->name('quiz.show');
    Route::post('/{slug}/quiz/regenerate', [BigOQuizController::class, 'regenerate'])
        ->middleware('throttle:10,1')
        ->name('quiz.regenerate');
});
