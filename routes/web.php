<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/learn', 'learn')->name('learn');
    Route::view('/track-spending', 'track-spending')->name('spending');
    Route::view('/progress', 'progress')->name('progress');
    Route::view('/town', 'town')->name('town');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/statistics', [StatsController::class, 'index'])
        ->name('statistics');

    Route::post('/clear-notification', function () {
        session()->forget('level_up');
        session()->forget('level_up_put');
        return response()->json(['success' => true]);
    })->name('notification.clear');

    Route::post('/update-coins', function (Request $request) {
        $user = auth()->user();
        $user->coins = $request->coins;
        $user->save();
        return response()->json(['success' => true, 'coins' => $user->coins]);
    })->name('coins.update');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('transactions.store');

    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])
        ->name('transactions.destroy');

    Route::get('/transactions/stats', [TransactionController::class, 'stats'])
        ->name('transactions.stats');

    Route::post('/lessons/complete', [LessonController::class, 'complete'])
        ->name('lessons.complete');

    Route::get('/user/lesson-progress', [LessonController::class, 'progress'])
        ->name('lessons.progress');

    Route::get('/quiz', [QuizController::class, 'index'])
        ->name('quiz.index');

    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])
        ->name('quiz.take');

    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])
        ->name('quiz.submit');

    Route::get('/quiz/{quiz}/results/{attempt}', [QuizController::class, 'results'])
        ->name('quiz.results');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/profile/delete', [ProfileController::class, 'delete'])
        ->name('profile.delete');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    
    Route::post('/api/game/update', function (Request $request) {
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        
        if ($request->has('coins')) $user->coins = $request->coins;
        if ($request->has('townLevel')) $user->town_level = $request->townLevel;
        if ($request->has('population')) $user->population = $request->population;
        if ($request->has('wood')) $user->wood = $request->wood;
        if ($request->has('stone')) $user->stone = $request->stone;
        if ($request->has('food')) $user->food = $request->food;
        
        $user->save();
        
        return response()->json(['success' => true, 'coins' => $user->coins]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
    Route::get('/api/game/coins', function (Request $request) {
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        return response()->json([
            'coins' => $user->coins,
            'level' => $user->level,
            'townLevel' => $user->level,
            'population' => $user->population ?? 0,
            'wood' => $user->wood ?? 120,
            'stone' => $user->stone ?? 80,
            'food' => $user->food ?? 50
        ]);
    });
});