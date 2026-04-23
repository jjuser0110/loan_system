<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/change_language/{language}', [App\Http\Controllers\HomeController::class, 'change_language'])->name('change_language');

Route::post('/change_password', [App\Http\Controllers\HomeController::class, 'change_password'])->name('change_password');
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');

// Staff dashboard only — staff CRUD is handled in the separate staff routes file
Route::middleware(['auth', 'check.login.time'])->group(function () {
    Route::get('/staff/home', [App\Http\Controllers\StaffDashboardController::class, 'index'])->name('staff.home');
});