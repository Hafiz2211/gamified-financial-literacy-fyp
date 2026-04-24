<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;  // 👈 ADDED THIS LINE ONLY
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FurnitureController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/learn', 'learn')->name('learn');
    Route::view('/track-spending', 'track-spending')->name('spending');
    Route::view('/progress', 'progress')->name('progress');
    Route::view('/town', 'town')->name('town');
    Route::view('/subscription', 'subscription')->name('subscription');
});

Route::middleware(['auth'])->group(function () { 

    Route::post('/subscribe/monthly', [SubscriptionController::class, 'checkoutMonthly'])->name('subscribe.monthly');
    Route::post('/subscribe/yearly', [SubscriptionController::class, 'checkoutYearly'])->name('subscribe.yearly');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    
    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');

    // 👇 REPLACED THIS ROUTE ONLY (added email sending)
    Route::post('/contact', function (Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ]);
        
        // Send email to site owner
        Mail::send([], [], function ($message) use ($request) {
            $message->to('brusavesite@gmail.com')
                    ->subject('New Contact Message from ' . $request->name)
                    ->replyTo($request->email)
                    ->html('
                        <h2>New Contact Message</h2>
                        <p><strong>Name:</strong> ' . e($request->name) . '</p>
                        <p><strong>Email:</strong> ' . e($request->email) . '</p>
                        <p><strong>Message:</strong></p>
                        <p>' . nl2br(e($request->message)) . '</p>
                    ');
        });
        
        session()->flash('success', 'Thank you for your message! We\'ll get back to you soon.');
        return redirect()->route('contact');
    })->name('contact.submit');
    // 👆 REPLACED THIS ROUTE ONLY

    Route::get('/furniture/load', [App\Http\Controllers\FurnitureController::class, 'load'])->name('furniture.load');
    Route::post('/furniture/save', [App\Http\Controllers\FurnitureController::class, 'save'])->name('furniture.save');
    
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
    
    // Get current logged-in user for Godot
    Route::get('/api/game/user', function (Request $request) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'force_cloud' => true
        ]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
    // Save buildings - USES AUTHENTICATED USER ONLY
    Route::post('/api/game/save', function (Request $request) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        $saveData = $request->all();
        unset($saveData['user_id']);
        
        $user->buildings_data = json_encode($saveData);
        $user->save();
        
        return response()->json(['success' => true, 'user_id' => $user->id]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
    // Load buildings
    Route::get('/api/game/load', function (Request $request) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        if (!$user->buildings_data || $user->buildings_data === 'null') {
            return response()->json([
                'buildings' => [],
                'success' => true
            ]);
        }
        
        $data = json_decode($user->buildings_data, true);
        
        if (isset($data['user_id'])) {
            unset($data['user_id']);
        }
        
        if (!isset($data['buildings'])) {
            $data['buildings'] = [];
        }
        
        $data['success'] = true;
        
        return response()->json($data);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
    // Update coins - USES AUTHENTICATED USER ONLY
    Route::post('/api/game/update', function (Request $request) {
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        if ($request->has('coins')) $user->coins = $request->coins;
        if ($request->has('townLevel')) $user->town_level = $request->townLevel;
        if ($request->has('population')) $user->population = $request->population;
        if ($request->has('wood')) $user->wood = $request->wood;
        if ($request->has('stone')) $user->stone = $request->stone;
        if ($request->has('food')) $user->food = $request->food;
        
        $user->save();
        
        return response()->json(['success' => true, 'coins' => $user->coins]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
    // Load coins - ALWAYS returns database value
    Route::get('/api/game/coins', function (Request $request) {
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        return response()->json([
            'coins' => (int)$user->coins,
            'level' => $user->level,
            'townLevel' => $user->town_level ?? 1,
            'population' => $user->population ?? 0,
            'wood' => $user->wood ?? 120,
            'stone' => $user->stone ?? 80,
            'food' => $user->food ?? 50,
            'timestamp' => time(),
            'source' => 'database'
        ]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
    // FORCE RESET - Clears corrupted game data for current user
    Route::post('/api/game/reset-my-data', function (Request $request) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');
        
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        $user->coins = 0;
        $user->buildings_data = null;
        $user->town_level = 1;
        $user->population = 0;
        $user->wood = 120;
        $user->stone = 80;
        $user->food = 50;
        $user->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Game data reset',
            'coins' => 0,
            'buildings' => [],
            'clear_local_storage' => true
        ]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});