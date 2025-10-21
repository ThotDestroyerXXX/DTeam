<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FriendController;
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
        Route::get('games', [GameController::class, 'index'])->name('publisher.games.index');
        Route::get('games/add', [GameController::class, 'add'])->name('publisher.games.add');
        Route::post('games', [GameController::class, 'store'])->name('publisher.games.store');
        Route::get('games/{game}/edit', [GameController::class, 'edit'])->name('publisher.games.edit');
        Route::put('games/{game}', [GameController::class, 'update'])->name('publisher.games.update');
        Route::delete('games/{game}', [GameController::class, 'destroy'])->name('publisher.games.destroy');
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
        // library routes
        Route::get('library', [LibraryController::class, 'index'])->name('user.library.index');

        // cart routes
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('user.cart.index');
            Route::post('{game}', [CartController::class, 'add'])->name('user.cart.add');
            Route::delete('{game}', [CartController::class, 'remove'])->name('user.cart.remove');
            Route::delete('/', [CartController::class, 'removeAll'])->name('user.cart.remove-all');
            Route::patch('{gameCart}/toggle-gift', [CartController::class, 'toggleGift'])->name('user.cart.toggle-gift');
        });

        // wishlist routes
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('user.wishlist.index');
            Route::post('{game}', [WishlistController::class, 'add'])->name('user.wishlist.add');
            Route::delete('{game}', [WishlistController::class, 'remove'])->name('user.wishlist.remove');
        });

        // checkout routes
        Route::prefix('checkout')->group(function () {
            Route::get('/', [CheckoutController::class, 'index'])->name('user.checkout.index');
            Route::post('/', [CheckoutController::class, 'process'])->name('user.checkout.process');
        });

        // transaction routes
        Route::get('transactions', [TransactionController::class, 'index'])->name('user.transaction.index');

        // wallet routes
        Route::prefix('wallet-codes')->group(function () {
            Route::get('/', [WalletCodeController::class, 'indexUser'])->name('user.wallet-code.index');
            Route::post('/', [WalletCodeController::class, 'redeem'])->name('user.wallet-code.redeem');
        });

        // Friends routes
        Route::prefix('friends')->group(function () {
            Route::get('/', [FriendController::class, 'index'])->name('user.friends.index');
            Route::get('add', [FriendController::class, 'add'])->name('user.friends.add');
            Route::get('search', [FriendController::class, 'searchFriends'])->name('user.friends.search');
            Route::get('pending', [FriendController::class, 'pending'])->name('user.friends.pending');
            Route::post('request/send', [FriendController::class, 'sendRequest'])->name('user.friends.request.send');
            Route::post('request/accept', [FriendController::class, 'acceptRequest'])->name('user.friends.request.accept');
            Route::post('request/decline', [FriendController::class, 'declineRequest'])->name('user.friends.request.decline');
            Route::delete('{user}/request/cancel', [FriendController::class, 'cancelRequest'])->name('user.friends.request.cancel');
            Route::get('{user}', [FriendController::class, 'show'])->name('user.friends.show');
            Route::get('{user}/mutual', [FriendController::class, 'mutual'])->name('user.friends.mutual');
        });
    });
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
    Route::get('games/{game}', [GameController::class, 'detail'])->name('games.detail');
});
Route::middleware('guest')->group(function () {
    // Authentication Routes
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
