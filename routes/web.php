<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
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
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:auth') // proteção contra credential stuffing
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:auth')
        ->name('register.post');

    // Recuperação de palavra-passe
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:password-reset')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:auth')
        ->name('password.update');
});
// --- AUTHENTICATED ROUTES (verificação de email pendente) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification (precisa de auth mas NÃO de "verified")
    Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Download de qualificações — gated por policy (utilizador próprio ou admin).
    Route::get('/qualifications/{qualification}/document', [\App\Http\Controllers\QualificationController::class, 'download'])
        ->name('app.qualification.document');
});

// --- ROUTES THAT REQUIRE VERIFIED EMAIL ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/app', [AppController::class, 'index'])->name('app.index');
    Route::resource('service', ServiceController::class)->names('app.service');
    Route::post('/services/{service}/accept', [ServiceController::class, 'accept'])->name('app.service.accept');
    Route::post('/services/{service}/dismiss', [ServiceController::class, 'dismiss'])->name('app.service.dismiss');
    Route::post('/services/{service}/complete', [ServiceController::class, 'markCompleted'])->name('app.service.complete');
    Route::get('/services/{service}/ics', [ServiceController::class, 'exportIcs'])->name('app.service.ics');
    Route::resource('user', UserController::class)->names('app.user')->except(['index', 'create', 'store']);
    Route::resource('review', ReviewController::class)->names('app.review');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('app.notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('app.notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('app.notifications.readAll');

    // Admin
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/qualifications', [\App\Http\Controllers\Admin\QualificationController::class, 'index'])->name('qualifications.index');
        Route::post('/qualifications/{qualification}/verify', [\App\Http\Controllers\Admin\QualificationController::class, 'verify'])->name('qualifications.verify');
        Route::post('/qualifications/{qualification}/reject', [\App\Http\Controllers\Admin\QualificationController::class, 'reject'])->name('qualifications.reject');
    });
});

// Fallback route for undefined paths
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});