<?php

use App\Http\Controllers\MeasurementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrialController;
use App\Http\Middleware\SetLocale;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware([SetLocale::class])->group(function () {
    Route::get('/', [TrialController::class, 'create'])->name('trial.create');
    Route::post('/trial', [TrialController::class, 'store'])->name('trial.store');
    Route::get('/trial/{trial}', [TrialController::class, 'show'])->name('trial.show');
    Route::get('/trials', [TrialController::class, 'index'])->name('trials.index');
    Route::get('/trial/{trial}/edit', [TrialController::class, 'edit'])->name('trial.edit');
    Route::put('/trial/{trial}', [TrialController::class, 'update'])->name('trial.update');
    Route::delete('/trial/{trial}', [TrialController::class, 'destroy'])->name('trial.destroy');

    Route::any('/{any}', function () {
        return redirect('/')->with('error', 'Page not found');
    })->where('any', '.*');
});
