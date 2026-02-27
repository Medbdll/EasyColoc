<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
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
    Route::match(['get', 'post'], '/colocations/{colocation}/leave', [ColocationController::class, 'leaveColocation'])->name('colocations.leave');
    Route::match(['get', 'post'], '/colocations/{colocation}/remove/{member}', [ColocationController::class, 'removeMember'])->name('colocations.remove');
    
    // Category routes
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Expense routes
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    
    // Invitation routes
    Route::post('/invitations/store', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/confirm/{token}', [InvitationController::class, 'confirmAccept'])->name('invitations.confirm');
    Route::get('/invitations/decline/{token}', [InvitationController::class, 'decline'])->name('invitations.decline');    
   
});
