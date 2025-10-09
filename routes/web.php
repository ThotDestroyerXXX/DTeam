<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WalletCodeController;
use App\Http\Middleware\CheckProfileCompletion;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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
    Route::get('profile/edit', [ProfileController::class, 'editPublisher'])->name('publisher.profile.edit');
    // Route::post('profile', [PublisherController::class, 'updateProfile'])->name('publisher.profile.update');
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
    });
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
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
