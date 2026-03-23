<?php

use App\Http\Controllers\Admin\BoxController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\QRGenerationController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::post('/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Boxes
    Route::get('/boxes', [BoxController::class, 'index'])->name('boxes.index');
    Route::get('/boxes/{id}', [BoxController::class, 'show'])->name('boxes.show');
    Route::post('/boxes/{id}/transfer', [BoxController::class, 'transfer'])->name('boxes.transfer');
    Route::post('/boxes/{id}/unclaim', [BoxController::class, 'unclaim'])->name('boxes.unclaim');
    Route::delete('/boxes/{id}', [BoxController::class, 'destroy'])->name('boxes.destroy');
    Route::post('/boxes/bulk-action', [BoxController::class, 'bulkAction'])->name('boxes.bulk-action');

    // QR Generation
    Route::get('/qr', [QRGenerationController::class, 'index'])->name('qr.index');
    Route::post('/qr/generate', [QRGenerationController::class, 'generate'])->name('qr.generate');

    // Locations
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::patch('/locations/{id}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');

    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Storage
    Route::get('/storage', [StorageController::class, 'index'])->name('storage.index');
    Route::delete('/storage', [StorageController::class, 'destroy'])->name('storage.destroy');

    // System
    Route::get('/system', [SystemController::class, 'index'])->name('system.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
