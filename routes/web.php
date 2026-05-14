<?php

use App\Http\Controllers\ConceptController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\GeneratedQuestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'))->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/domains/archived', [DomainController::class, 'archived'])->name('domains.archived');
    Route::patch('/domains/{domain}/restore', [DomainController::class, 'restore'])->name('domains.restore');
    Route::delete('/domains/{domain}/force-delete', [DomainController::class, 'forceDelete'])->name('domains.forceDelete');
    Route::resource('domains', DomainController::class)->except(['index', 'archived']);
    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');

    Route::get('/domains/{domain}/concepts', [ConceptController::class, 'index'])->name('domains.concepts.index');
    Route::patch('/domains/{domain}/concepts/{concept}/restore', [ConceptController::class, 'restore'])->name('domains.concepts.restore');
    Route::delete('/domains/{domain}/concepts/{concept}/force-delete', [ConceptController::class, 'forceDelete'])->name('domains.concepts.forceDelete');
    Route::patch('/domains/{domain}/concepts/{concept}/status', [ConceptController::class, 'updateStatus'])->name('domains.concepts.updateStatus');
    Route::resource('domains.concepts', ConceptController::class)->except(['index']);

    Route::post('/concepts/{concept}/generate', [GeneratedQuestionController::class, 'store'])->name('generated-questions.store');
    Route::delete('/generated-questions/{generatedQuestion}', [GeneratedQuestionController::class, 'destroy'])->name('generated-questions.destroy');
    Route::patch('/generated-questions/{id}/restore', [GeneratedQuestionController::class, 'restore'])->name('generated-questions.restore');
    Route::delete('/generated-questions/{id}/force-delete', [GeneratedQuestionController::class, 'forceDelete'])->name('generated-questions.forceDelete');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';