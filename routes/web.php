<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Redirect root to /admin
Route::get('/', function () {
    return redirect('/admin');
});

// Main admin route - shows Welcome.vue with modal if not authenticated, Dashboard if authenticated
Route::get('/admin', function () {
    if (Auth::check()) {
        // User is authenticated, show Dashboard
        return Inertia::render('Dashboard');
    } else {
        // User is not authenticated, show Welcome page with auto-open modal
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'autoOpenModal' => true, // Signal to auto-open login modal
        ]);
    }
})->name('admin');

// Authenticated admin routes - require auth and verified
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Crop Management Routes
    Route::resource('crops', \App\Http\Controllers\CropController::class);
    Route::post('/crops/generate-batch-code', [\App\Http\Controllers\CropController::class, 'generateBatchCode'])
        ->name('crops.generate-batch-code');
    Route::post('/crops/{crop}/advance-stage', [\App\Http\Controllers\CropController::class, 'advanceStage'])
        ->name('crops.advance-stage');
    Route::post('/crops/bulk-advance-stage', [\App\Http\Controllers\CropController::class, 'bulkAdvanceStage'])
        ->name('crops.bulk-advance-stage');
    Route::post('/crops/bulk-advance-batches', [\App\Http\Controllers\CropController::class, 'bulkAdvanceBatches'])
        ->name('crops.bulk-advance-batches');
    Route::post('/crops/bulk-delete', [\App\Http\Controllers\CropController::class, 'bulkDelete'])
        ->name('crops.bulk-delete');
    Route::post('/crops/bulk-delete-batches', [\App\Http\Controllers\CropController::class, 'bulkDeleteBatches'])
        ->name('crops.bulk-delete-batches');
});

require __DIR__.'/auth.php';
