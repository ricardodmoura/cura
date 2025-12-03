<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Carbon;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/terms-of-service', function () {
    return view('legal.terms-of-service');
})->name('terms-of-service');
Route::get('/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('privacy-policy');
Route::get('/cookie-policy', function () {
    return view('legal.cookie-policy');
})->name('cookie-policy');

// --- GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/app', [AppController::class, 'index'])->name('app.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('service', ServiceController::class)->names('app.service');
    Route::post('/services/{service}/accept', [ServiceController::class, 'accept'])->name('app.service.accept');
    Route::resource('profile', ProfileController::class)->names('app.profile')->except(['index', 'create', 'store']);
    Route::resource('review', ReviewController::class)->names('app.review');
});

// Fallback route for undefined paths
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});