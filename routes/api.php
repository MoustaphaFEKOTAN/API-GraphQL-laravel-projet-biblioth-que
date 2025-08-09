<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


// --------------------------------------------------------------------------------------------------------------------------------


// Email Vérification
Route::get('/email/verify/{id}/{hash}', [AuthenticatedSessionController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthenticatedSessionController::class, 'sendVerificationEmail'])
    ->middleware(['auth:sanctum'])
    ->name('verification.send');

// Mot de passe oublié / reset
Route::post('/forgot-password', [AuthenticatedSessionController::class, 'forgotPassword'])
    ->name('api.forgot-password');

Route::post('/reset-password', [AuthenticatedSessionController::class, 'resetPassword'])
    ->name('api.reset-password');

    //Update password
Route::middleware('auth:sanctum')->post('/change-password', [AuthenticatedSessionController::class, 'changePassword'])
    ->name('api.change-password');


