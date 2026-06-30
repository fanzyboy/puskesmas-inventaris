<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Pastikan berada di dalam grup middleware auth
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-room', [DashboardController::class, 'switchRoom'])->name('switch.room');
    
    Route::resource('items', ItemController::class);
    Route::resource('borrowings', BorrowingController::class);
    Route::resource('rooms', RoomController::class);
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::resource('reports', ReportController::class);
});