<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ItemCommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;

Route::get('/', fn () => view('welcome'));

// 認証
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 商品一覧・詳細（公開）
Route::get('/items',       [ItemController::class, 'index'])->middleware('tab.auth')->name('items.index');
Route::get('/items/{item}',[ItemController::class, 'show'])->name('items.show');

// 認証が必要なルート
Route::middleware('auth')->group(function () {

    // プロフィール
    Route::get('/profile/edit',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // 出品
    Route::get('/sell',  [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // マイページ
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');

    // いいね
    Route::post('/items/{item}/like', [LikeController::class, 'toggle'])->name('items.like.toggle');

    // コメント投稿（統一）
    Route::post('/items/{item}/comments', [ItemCommentController::class, 'store'])
        ->name('items.comments.store');

    // 購入画面／購入確定
    Route::get ('/items/{item}/buy', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/items/{item}/buy', [PurchaseController::class, 'store'])->name('purchase.store');

    // 送付先住所（AddressController に統一）
    Route::get('/address/edit',  [AddressController::class, 'edit'])->name('address.edit');
    Route::patch('/address',     [AddressController::class, 'update'])->name('address.update');



});
