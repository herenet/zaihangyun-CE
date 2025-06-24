<?php

use Illuminate\Support\Facades\Route;
use App\SaaSAdmin\Controllers\ShowController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\SubscriptionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [WebsiteController::class, 'index']);
Route::get('/pricing', [WebsiteController::class, 'pricing']);
Route::get('/about', [WebsiteController::class, 'about']);

// 套餐购买相关路由
Route::middleware(['web'])->group(function () {
    Route::get('/subscription/confirm', [SubscriptionController::class, 'confirm'])->name('subscription.confirm');
    Route::post('/subscription/create-order', [SubscriptionController::class, 'createOrder'])->name('subscription.create-order');
    Route::get('/subscription/payment/{orderId}', [SubscriptionController::class, 'payment'])->name('subscription.payment');
    Route::get('/subscription/order-status/{orderId}', [SubscriptionController::class, 'queryOrderStatus'])->name('subscription.order-status');
});

Route::get('/article/{app_key}/{id}', [ShowController::class, 'show'])->name('article.show');
Route::get('/article/category/{app_key}/{id}', [ShowController::class, 'category'])->name('article.category.show');
Route::get('/article/category/{app_key}/{id}/load', [ShowController::class, 'category'])->name('article.category.load');