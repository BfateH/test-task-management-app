<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('home');
    Route::post('/', [MainController::class, 'store'])->name('tasks.store');
    Route::get('/create', [MainController::class, 'create'])->name('tasks.create');
    Route::patch('/changeStatus/{task}', [MainController::class, 'changeStatus'])->name('tasks.changeStatus');
    Route::get('/nextStatus/{task}', [MainController::class, 'nextStatus'])->name('tasks.nextStatus');
    Route::get('/toArchive/{task}', [MainController::class, 'toArchive'])->name('tasks.toArchive');
    Route::get('/edit/{task}', [MainController::class, 'edit'])->name('tasks.edit');
    Route::patch('/{task}', [MainController::class, 'update'])->name('tasks.update');
    Route::delete('/{task}', [MainController::class, 'delete'])->name('tasks.delete');
});

Auth::routes();
