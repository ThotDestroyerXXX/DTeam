<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WalletCodeController;
use Illuminate\Support\Facades\Route;

// Admin routes
Route::middleware('can:is-admin')->prefix('admin')->group(function () {
    Route::get('publishers', [PublisherController::class, 'index'])->name('admin.publishers.index');
    Route::get('wallet-codes', [WalletCodeController::class, 'index'])->name('admin.wallet-codes.index');
    Route::get('genres', [GenreController::class, 'index'])->name('admin.genres.index');
    // Route::get('publishers/create', [PublisherController::class, 'create'])->name('admin.publishers.create');
    // Route::post('publishers', [PublisherController::class, 'store'])->name('admin.publishers.store');
});

// publisher routes
Route::middleware('can:is-publisher')->prefix('publisher')->group(function () {
    Route::get('games', [GameController::class, 'index'])->name('publisher.games.index');
    Route::get('profile/edit', [PublisherController::class, 'editProfile'])->name('publisher.profile.edit');
    // Route::post('profile', [PublisherController::class, 'updateProfile'])->name('publisher.profile.update');
    // Route::get('password/change', [PublisherController::class, 'changePassword'])->name('publisher.password.change');
    // Route::post('logout', [PublisherController::class, 'logout'])->name('publisher.logout');
});

// user routes
Route::middleware('can:is-user')->prefix('user')->group(function () {
    Route::get('library', [LibraryController::class, 'index'])->name('user.library.index');
});

Route::get('/', [StoreController::class, 'index'])->name('store.index');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
});
