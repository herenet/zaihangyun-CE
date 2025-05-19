<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('config/wechat/payment/check-callback', 'Manager\WechatPaymentConfigController@checkCallback')
    ->name('wechat.payment.check-callback');

Route::post('wechat/refund/notify/{encodeNotifyParams}', 'Manager\OrderController@wechatRefundCallback')
    ->name('wechat.refund.notify');

Route::post('ali/refund/notify/{encodeNotifyParams}', 'Manager\OrderController@aliRefundCallback')
    ->name('ali.refund.notify');

Route::post('apple/refund/notify/{encodeNotifyParams}', 'Manager\OrderController@appleRefundCallback')
    ->name('apple.refund.notify');
