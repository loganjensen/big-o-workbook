<?php

use App\Http\Controllers\BigO\ShowO1Controller;
use App\Http\Controllers\BigO\ShowO2NController;
use App\Http\Controllers\BigO\ShowOLogNController;
use App\Http\Controllers\BigO\ShowONController;
use App\Http\Controllers\BigO\ShowONFactorialController;
use App\Http\Controllers\BigO\ShowONLogNController;
use App\Http\Controllers\BigO\ShowONSquaredController;
use App\Http\Controllers\BigOController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

Route::get('/', fn () => Inertia::render('Welcome'));

Route::get('/big-o', [BigOController::class, 'index'])->name('big-o.index');
Route::get('/o-1', ShowO1Controller::class)->name('big-o.o-1');
Route::get('/o-log-n', ShowOLogNController::class)->name('big-o.o-log-n');
Route::get('/o-n', ShowONController::class)->name('big-o.o-n');
Route::get('/o-n-log-n', ShowONLogNController::class)->name('big-o.o-n-log-n');
Route::get('/o-n-squared', ShowONSquaredController::class)->name('big-o.o-n-squared');
Route::get('/o-2-n', ShowO2NController::class)->name('big-o.o-2-n');
Route::get('/o-n-factorial', ShowONFactorialController::class)->name('big-o.o-n-factorial');

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
