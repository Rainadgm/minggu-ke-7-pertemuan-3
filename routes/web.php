<?php

use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $products = Product::query()
        ->orderBy('name')
        ->get(['id', 'name', 'category', 'price', 'stock', 'is_active']);

    return view('dashboard', compact('products'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/products', fn () => redirect()->route('dashboard'))->name('products.index');
    Route::get('/products/create', fn () => redirect()->route('dashboard'))->name('products.create');
    Route::resource('products', ProductController::class)->except(['index', 'create', 'show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
