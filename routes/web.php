<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\QuizController; // Add this import!

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('/learn', 'learn')
    ->middleware(['auth', 'verified'])
    ->name('learn');

Route::view('/track-spending', 'track-spending')
    ->middleware(['auth', 'verified'])
    ->name('spending');

Route::view('/progress', 'progress')
    ->middleware(['auth', 'verified'])
    ->name('progress');

Route::view('/town', 'town')
    ->middleware(['auth', 'verified'])
    ->name('town');

Route::get('/statistics', [StatsController::class, 'index'])
    ->middleware(['auth'])
    ->name('statistics');

Route::middleware(['auth'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::post('/lessons/complete', [LessonController::class, 'complete'])->name('lessons.complete');
    Route::get('/user/lesson-progress', [LessonController::class, 'progress'])->name('lessons.progress');
    Route::get('/transactions/stats', [TransactionController::class, 'stats'])->name('transactions.stats');
    Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/results/{attempt}', [QuizController::class, 'results'])->name('quiz.results');
});