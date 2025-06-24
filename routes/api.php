<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\SaaSAdmin\Controllers\Manager\OrderController;
use App\Http\Controllers\API\WechatPayCallbackController;
use App\SaaSAdmin\Controllers\AppleApiCertConfigController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('sandbox/apple/verify/notify/{params}', [AppleApiCertConfigController::class, 'verifyNotify'])
//     ->name('apple.verify-notify');

// 微信支付回调（套餐购买）
Route::post('AXstsastaxa/wechat/pay/callback', [WechatPayCallbackController::class, 'handle'])
    ->name('wechat.pay.callback');
