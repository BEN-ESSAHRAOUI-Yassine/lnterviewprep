<?php

use App\Http\Controllers\DomainController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'))->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
});

require __DIR__.'/auth.php';