<?php

use App\Http\Controllers\ColocationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    
    // Admin user management routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
    });
    
    // Colocation routes
    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations/store', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');    
   
});
