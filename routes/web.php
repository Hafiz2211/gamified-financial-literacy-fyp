<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('/learn', 'learn')
    ->middleware(['auth', 'verified'])
    ->name('learn');

Route::view('/quiz', 'quiz')
    ->middleware(['auth', 'verified'])
    ->name('quiz');

Route::view('/track-spending', 'track-spending')
    ->middleware(['auth', 'verified'])
    ->name('spending');

Route::view('/progress', 'progress')
    ->middleware(['auth', 'verified'])
    ->name('progress');

Route::view('/town', 'town')
    ->middleware(['auth', 'verified'])
    ->name('town');

require __DIR__.'/settings.php';

