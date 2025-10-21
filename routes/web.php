<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletCodeController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\CheckProfileCompletion;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Admin routes
Route::middleware('can:is-admin')->prefix('admin')->group(function () {
    Route::prefix('publishers')->group(function () {
        Route::get('/', [PublisherController::class, 'index'])->name('admin.publishers.index');
        Route::get('add', [PublisherController::class, 'add'])->name('admin.publishers.add');
        // Route::post('/', [PublisherController::class, 'store'])->name('admin.publishers.store');
    });
    Route::prefix('wallet-codes')->group(function () {
        Route::get('/', [WalletCodeController::class, 'index'])->name('admin.wallet-codes.index');
        Route::get('add', [WalletCodeController::class, 'add'])->name('admin.wallet-codes.add');
        Route::post('/', [WalletCodeController::class, 'store'])->name('admin.wallet-codes.store');
    });

    Route::prefix('genres')->group(function () {
        Route::get('/', [GenreController::class, 'index'])->name('admin.genres.index');
        Route::get('add', [GenreController::class, 'add'])->name('admin.genres.add');
        Route::post('/', [GenreController::class, 'store'])->name('admin.genres.store');
        Route::patch('{genre}', [GenreController::class, 'destroy'])->name('admin.genres.destroy');
        Route::get('edit/{genre}', [GenreController::class, 'edit'])->name('admin.genres.edit');
        Route::put('{genre}', [GenreController::class, 'update'])->name('admin.genres.update');
    });
});

// publisher routes
Route::middleware(CheckProfileCompletion::class)->group(function () {
    Route::middleware('can:is-publisher')->prefix('publisher')->group(function () {
        Route::get('games', [App\Http\Controllers\Publisher\GameController::class, 'index'])->name('publisher.games.index');
        Route::get('games/add', [App\Http\Controllers\Publisher\GameController::class, 'add'])->name('publisher.games.add');
        Route::post('games', [App\Http\Controllers\Publisher\GameController::class, 'store'])->name('publisher.games.store');
        Route::get('games/{game}/edit', [App\Http\Controllers\Publisher\GameController::class, 'edit'])->name('publisher.games.edit');
        Route::put('games/{game}', [App\Http\Controllers\Publisher\GameController::class, 'update'])->name('publisher.games.update');
        Route::delete('games/{game}', [App\Http\Controllers\Publisher\GameController::class, 'destroy'])->name('publisher.games.destroy');
    });
});

Route::middleware('can:is-publisher')->prefix('publisher')->group(function () {
    Route::get('profile/edit', [ProfileController::class, 'editPublisher'])->name('publisher.profile.edit');
    Route::put('profile', [ProfileController::class, 'updatePublisher'])->name('publisher.profile.update');
    // Route::get('password/change', [PublisherController::class, 'changePassword'])->name('publisher.password.change');
    // Route::post('logout', [PublisherController::class, 'logout'])->name('publisher.logout');
});

// Profile edit route must be outside profile completion middleware to avoid infinite redirect
Route::middleware(['auth', 'can:is-user'])->prefix('user')->group(function () {
    Route::get('profile/edit', [ProfileController::class, 'editUser'])->name('user.profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('user.profile.update');
});

// user routes that require complete profile
Route::middleware(CheckProfileCompletion::class)->group(function () {
    Route::middleware('can:is-user')->prefix('user')->group(function () {
        Route::get('library', [LibraryController::class, 'index'])->name('user.library.index');
        Route::get('cart', [CartController::class, 'index'])->name('user.cart.index');
        Route::post('cart/{game}', [CartController::class, 'add'])->name('user.cart.add');
        Route::delete('cart/{game}', [CartController::class, 'remove'])->name('user.cart.remove');
        Route::delete('cart', [CartController::class, 'removeAll'])->name('user.cart.remove-all');
        Route::patch('cart/{gameCart}/toggle-gift', [CartController::class, 'toggleGift'])->name('user.cart.toggle-gift');
        Route::get('wishlist', [WishlistController::class, 'index'])->name('user.wishlist.index');
        Route::post('wishlist/{game}', [WishlistController::class, 'add'])->name('user.wishlist.add');
        Route::delete('wishlist/{game}', [WishlistController::class, 'remove'])->name('user.wishlist.remove');
        Route::get('checkout', [CheckoutController::class, 'index'])->name('user.checkout.index');
        Route::post('checkout', [CheckoutController::class, 'process'])->name('user.checkout.process');
        Route::get('transactions', [TransactionController::class, 'index'])->name('user.transaction.index');
        Route::get('wallet-codes', [WalletCodeController::class, 'indexUser'])->name('user.wallet-code.index');
        Route::post('wallet-codes', [WalletCodeController::class, 'redeem'])->name('user.wallet-code.redeem');
    });
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
    Route::get('games/{game}', [GameController::class, 'detail'])->name('games.detail');
});
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->name('register.perform');
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
