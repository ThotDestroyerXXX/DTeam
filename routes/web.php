<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameGiftController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletCodeController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\CheckProfileCompletion;
use App\Http\Controllers\PasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProfileController as UserProfileController;

// Admin routes
Route::middleware('can:is-admin')->prefix('admin')->group(function () {
    Route::prefix('publishers')->controller(PublisherController::class)->group(function () {
        Route::get('/', 'index')->name('admin.publishers.index');
        Route::get('add', 'add')->name('admin.publishers.add');
        Route::post('/', 'store')->name('admin.publishers.store');
    });
    Route::prefix('wallet-codes')->controller(WalletCodeController::class)->group(function () {
        Route::get('/', 'index')->name('admin.wallet-codes.index');
        Route::get('add', 'add')->name('admin.wallet-codes.add');
        Route::post('/', 'store')->name('admin.wallet-codes.store');
    });

    Route::prefix('genres')->controller(GenreController::class)->group(function () {
        Route::get('/', 'index')->name('admin.genres.index');
        Route::get('add', 'add')->name('admin.genres.add');
        Route::post('/', 'store')->name('admin.genres.store');
        Route::patch('{genre}', 'destroy')->name('admin.genres.destroy');
        Route::get('edit/{genre}', 'edit')->name('admin.genres.edit');
        Route::put('{genre}', 'update')->name('admin.genres.update');
    });
});

// publisher routes
Route::middleware(CheckProfileCompletion::class)->group(function () {
    Route::middleware('can:is-publisher')->prefix('publisher')->controller(GameController::class)->group(function () {
        Route::get('games', 'index')->name('publisher.games.index');
        Route::get('games/add', 'add')->name('publisher.games.add');
        Route::post('games', 'store')->name('publisher.games.store');
        Route::get('games/{game}/edit', 'edit')->name('publisher.games.edit');
        Route::put('games/{game}', 'update')->name('publisher.games.update');
        Route::delete('games/{game}', 'destroy')->name('publisher.games.destroy');
    });
});

Route::middleware('can:is-publisher')->prefix('publisher')->controller(ProfileController::class)->group(function () {
    Route::get('profile/edit', 'editPublisher')->name('publisher.profile.edit');
    Route::put('profile', 'updatePublisher')->name('publisher.profile.update');
    // Route::get('password/change', 'changePassword')->name('publisher.password.change');
    // Route::post('logout', 'logout')->name('publisher.logout');
});

// Profile edit route must be outside profile completion middleware to avoid infinite redirect
Route::middleware(['auth', 'can:is-user'])->prefix('user')->controller(ProfileController::class)->group(function () {
    Route::get('profile/setup', 'setupUser')->name('user.profile.setup');
    Route::put('profile', 'update')->name('user.profile.update');
});

// user routes that require complete profile
Route::middleware(CheckProfileCompletion::class)->group(function () {
    Route::middleware('can:is-user')->prefix('user')->group(function () {
        // library routes
        Route::get('library', [LibraryController::class, 'index'])->name('user.library.index');

        // game gift routes
        Route::prefix('game-gifts')->controller(GameGiftController::class)->group(function () {
            Route::get('/', 'index')->name('user.game-gift.index');
            Route::post('/{sender}', 'store')->name('user.game-gift.store');
        });

        // cart routes
        Route::prefix('cart')->controller(CartController::class)->group(function () {
            Route::get('/', 'index')->name('user.cart.index');
            Route::post('{game}', 'add')->name('user.cart.add');
            Route::delete('{game}', 'remove')->name('user.cart.remove');
            Route::delete('/', 'removeAll')->name('user.cart.remove-all');
            Route::patch('{gameCart}/toggle-gift', 'toggleGift')->name('user.cart.toggle-gift');
        });

        // wishlist routes
        Route::prefix('wishlist')->controller(WishlistController::class)->group(function () {
            Route::get('/', 'index')->name('user.wishlist.index');
            Route::post('{game}', 'add')->name('user.wishlist.add');
            Route::delete('{game}', 'remove')->name('user.wishlist.remove');
        });

        // checkout routes
        Route::prefix('checkout')->controller(CheckoutController::class)->group(function () {
            Route::get('/', 'index')->name('user.checkout.index');
            Route::post('/', 'process')->name('user.checkout.process');
        });

        // transaction routes
        Route::get('transactions', [TransactionController::class, 'index'])->name('user.transaction.index');

        // wallet routes
        Route::prefix('wallet-codes')->controller(WalletCodeController::class)->group(function () {
            Route::get('/', 'indexUser')->name('user.wallet-code.index');
            Route::post('/', 'redeem')->name('user.wallet-code.redeem');
        });

        // Friends routes
        Route::prefix('friends')->controller(FriendController::class)->group(function () {
            Route::get('/', 'index')->name('user.friends.index');
            Route::get('add', 'add')->name('user.friends.add');
            Route::get('search', 'searchFriends')->name('user.friends.search');
            Route::get('pending', 'pending')->name('user.friends.pending');
            Route::post('request/send', 'sendRequest')->name('user.friends.request.send');
            Route::post('request/accept', 'acceptRequest')->name('user.friends.request.accept');
            Route::post('request/decline', 'declineRequest')->name('user.friends.request.decline');
            Route::delete('{user}/request/cancel', 'cancelRequest')->name('user.friends.request.cancel');
            Route::get('{user}', 'show')->name('user.friends.show');
            Route::get('{user}/mutual', 'mutual')->name('user.friends.mutual');
        });

        Route::prefix('point-shop')->controller(ItemController::class)->group(function () {
            Route::get('/', 'index')->name('user.point-shop.index');
            Route::post('purchase/{item}', 'purchase')->name('user.point-shop.purchase');
        });

        Route::prefix('profile')->group(function () {
            Route::controller(UserProfileController::class)->group(function () {
                Route::put('avatar', 'updateAvatar')->name('user.profile.update.avatar');
                Route::put('background', 'updateBackground')->name('user.profile.update.background');
                Route::put('general', 'updateGeneral')->name('user.profile.update.general');
                Route::put('password', 'updatePassword')->name('user.profile.update.password');
                Route::get('edit/{section?}', 'edit')->name('user.profile.edit.section');
            });
            Route::controller(ProfileController::class)->group(function () {
                Route::get('{user}', 'index')->name('user.profile.index');
            });
        });
    });
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
    Route::get('/publisher/{publisher}', [PublisherController::class, 'detail'])->name('publisher.detail');
    Route::get('games/{game}', [GameController::class, 'detail'])->name('games.detail');
});
Route::middleware('guest')->group(function () {
    // Authentication Routes
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->name('register.perform');

    // Forgot password / OTP flow (guest only)
    Route::prefix('forgot-password')->controller(PasswordController::class)->group(function () {
        Route::get('/', 'index')->name('password.index');
        Route::post('/', 'sendResetLinkEmail')->name('password.email');
        Route::get('/{token}', 'showResetForm')->name('password.reset');
        Route::post('/reset', 'resetPassword')->name('password.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    // route that accepts a section parameter for deep linking to profile partials

    // Avatar update route (allows PUT or POST for browser forms)

});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('guest')->prefix('forgot-password')->controller(PasswordController::class)->group(function () {
    Route::get('/', 'index')->name('password.index');
    Route::post('/', 'sendResetLinkEmail')->name('password.email');
    Route::get('/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset', 'resetPassword')->name('password.update');
});
