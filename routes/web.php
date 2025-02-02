<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InvoiceController;
use App\Models\Product;
use App\Http\Controllers\EmailVerificationPromptController;
use App\Http\Controllers\Api\ProductVariationsController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('verify-email', [EmailVerificationPromptController::class, 'show'])
        ->name('verification.notice');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    });
});

Route::middleware(['auth', 'role:wholesale'])->group(function () {
    Route::get('/wholesale/pricing', function () {
        return Inertia::render('Wholesale/Pricing');
    });
});

Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])
    ->name('invoice.print')
    ->middleware(['auth']);

Route::middleware([
    'auth',
    config('filament.middleware.base'),
])->group(function () {
    Route::get('/admin/api/product-variations/{product}', [ProductVariationsController::class, 'index']);
});

require __DIR__.'/auth.php';
