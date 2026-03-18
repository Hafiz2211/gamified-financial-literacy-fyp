<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request; 

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

Route::post('/clear-notification', function() {
    session()->forget('level_up');
    session()->forget('level_up_put');
    return response()->json(['success' => true]);
})->middleware('auth');

Route::post('/update-coins', function(Request $request) {
    $user = auth()->user();
    $user->coins = $request->coins;
    $user->save();
    
    return response()->json([
        'success' => true,
        'coins' => $user->coins
    ]);
})->middleware('auth');

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
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/delete', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});