<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LivreController;
use App\Models\Role;
use Illuminate\Http\Request;


// --------------------------------------------------------------------------------------------------------------------------------


//    LES ROUTES POUR LES ACTIONS SUR LA TABLE USERS
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);


//Déconnexion
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    //  $request->user()->tokens()->delete(); Déconnecter de toutes les sessions

    return response()->json(['message' => 'Déconnecté avec succès.']);
});



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


// --------------------------------------------------------------------------------------------------------------------------------

// liste des role a envoyé au formulaire d'inscription 
Route::get('/roles', function () {
    return Role::select('id', 'nom')->get();
});


// Route::get('/test', function () {
//     return 'ceci est un test réussi';
// });