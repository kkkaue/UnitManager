<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('units.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('units', \App\Http\Controllers\UnitController::class);
    Route::get('/map', [\App\Http\Controllers\UnitController::class, 'map'])->name('units.map');
    Route::post('/units/update-hierarchy', [\App\Http\Controllers\UnitController::class, 'updateHierarchy'])->name('units.update-hierarchy');
});

require __DIR__ . '/auth.php';
