<?php

use Illuminate\Auth\Notifications\VerifyEmail;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ProductController::class, 'index']);
Route::get('/item/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('email/verify', [AuthController::class, 'verify'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
});
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'setupProfile'])->name('profile.setup');
    Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('updateProfile');
    Route::get('/mypage/purchases', [UserController::class, 'showPurchasedItems']);
    Route::get('/mypage/sold', [UserController::class, 'showSoldItems']);
    Route::post('/products/{product}/like', [ProductController::class, 'like'])->name('products.like');
    Route::post('/comments/{productId}', [ProductController::class, 'store'])->name('comments.store');
    Route::get('/sell', [ExhibitionController::class, 'create']);
    Route::post('/sell', [ExhibitionController::class, 'store'])->name('sell');
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.process');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::post('/purchase/{item_id}/payment', [PurchaseController::class, 'processPayment']);
    Route::post('/purchase/update-payment/{item_id}', [PurchaseController::class, 'updatePayment'])->name('purchase.updatePayment');
});
