<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

// 🔹 商品一覧（トップ画面）
Route::get('/', [ProductController::class, 'index']);

// 🔹 商品詳細
Route::get('/item/{id}', [ProductController::class, 'show']);




// 🔹 認証関連
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail']);



// 🔹 マイページ関連（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'setupProfile'])->name('profile.setup');
    Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('updateProfile');

    // 購入履歴
    Route::get('/mypage/purchases', [UserController::class, 'showPurchasedItems']);

    // 出品履歴
    Route::get('/mypage/sold', [UserController::class, 'showSoldItems']);



    // マイリスト（お気に入り商品一覧）
    Route::get('/mylist', [ProductController::class, 'mylist']);

    // 商品にいいねを付けるルート
    Route::post('/products/{product}/like', [ProductController::class, 'like'])->name('products.like');
    Route::post('/comments/{productId}', [ProductController::class, 'store'])->name('comments.store');

    // 商品出品
    Route::get('/sell', [ExhibitionController::class, 'create']);
    Route::post('/sell', [ExhibitionController::class, 'store'])->name('sell');

    // 送付先住所変更
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.process');

    // 住所変更ページのルート
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address');

    // 住所更新処理のルート
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');


    // 支払い処理
    Route::post('/purchase/{item_id}/payment', [PurchaseController::class, 'processPayment']);

    Route::post('/purchase/update-payment/{item_id}', [PurchaseController::class, 'updatePayment'])->name('purchase.updatePayment');
    Route::post('/purchase/process/{item_id}', [PurchaseController::class, 'processPurchase'])->name('purchase.process');
});
