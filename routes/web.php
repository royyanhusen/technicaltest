<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\SessionTimeout;
use Illuminate\Support\Facades\Route;

// Route untuk welcome page (bisa diakses oleh siapa saja)
Route::get('/', function () {
    return view('welcome');
});

// Route untuk dashboard, membutuhkan user yang sudah login dan verified
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group route dengan middleware 'web', 'auth', dan 'session.timeout'
Route::middleware(['web', 'auth', 'session.timeout'])->group(function () {
    // Route untuk profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk memperbarui waktu aktivitas terakhir
    Route::post('/update_last_activity', [AuthController::class, 'updateLastActivity'])->name('update_last_activity');

    // Resource route untuk TaskController (CRUD tasks)
    Route::resource('tasks', TaskController::class);
});

// Memuat file routes untuk authentication (seperti login, register, dll)
require __DIR__.'/auth.php';