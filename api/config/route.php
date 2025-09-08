<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;
Route::disableDefaultRoute();
Route::group('/v1', function () {

    Route::group('', function () {
        Route::get('/user/info', [app\controller\api\UserController::class, 'info']);
        Route::post('/user/update', [app\controller\api\UserController::class, 'update']);
        Route::post('/user/avatar', [app\controller\api\UserController::class, 'avatar']);
        Route::get('/user/logout', [app\controller\api\UserController::class, 'logout']);
        Route::post('/user/verify_code', [app\controller\api\UserController::class, 'verifyCode']);
        Route::post('/user/mobile', [app\controller\api\UserController::class, 'mobile']);
        Route::post('/user/cancel', [app\controller\api\UserController::class, 'cancel']);
       
        Route::post('/order/create', [app\controller\api\OrderController::class, 'create']);
        Route::post('/order/apple/create', [app\controller\api\AppleOrderController::class, 'create']);
        Route::post('/order/apple/verify', [app\controller\api\AppleOrderController::class, 'verify']);
        Route::get('/order/config/apple/callback-verify-status', [app\controller\api\AppleOrderController::class, 'getAppleCallbackVerifyStatus']);

        Route::post('/order/callback/apple/{params}', [app\controller\api\AppleOrderController::class, 'notify']);
        Route::post('/order/callback/wechat/{params}', [app\controller\api\OrderController::class, 'wechatCallback']);
        Route::any('/order/callback/alipay/{params}', [app\controller\api\OrderController::class, 'alipayCallback']);
        Route::post('/order/callback/wechat/refund/{encodeNotifyParams}', [app\controller\api\OrderController::class, 'wechatRefundCallback']);
        
        Route::get('/order/list', [app\controller\api\OrderController::class, 'myOrder']);
        Route::get('/order/info', [app\controller\api\OrderController::class, 'info']);
        Route::get('/order/apple/info', [app\controller\api\AppleOrderController::class, 'info']);
        Route::get('/order/apple/list', [app\controller\api\AppleOrderController::class, 'myOrder']);
       
        Route::post('/message/feedback', [app\controller\api\MessageController::class, 'feedback']);
        Route::get('/message/feedback/my', [app\controller\api\MessageController::class, 'myFeedbackList']);
        Route::get('/message/feedback/list', [app\controller\api\MessageController::class, 'feedbackList']);

    })->middleware([app\middleware\ApiTokenCheck::class]);

    Route::group('', function () {
        Route::post('/login/wechat', [app\controller\api\LoginController::class, 'wechat']);
        Route::post('/login/verify_code', [app\controller\api\LoginController::class, 'verifyCode']);
        Route::post('/login/mobile', [app\controller\api\LoginController::class, 'mobile']);
        Route::post('/login/apple', [app\controller\api\LoginController::class, 'apple']);
        Route::post('/login/huawei', [app\controller\api\LoginController::class, 'huawei']);

        Route::get('/article/info', [app\controller\api\ArticleController::class, 'info']);
        Route::get('/article/list', [app\controller\api\ArticleController::class, 'list']);
        Route::get('/article/category/list', [app\controller\api\ArticleController::class, 'categoryList']);

        Route::get('/product/list', [app\controller\api\ProductController::class, 'list']);
        Route::get('/product/iap/list', [app\controller\api\ProductController::class, 'iapList']);
        Route::get('/product/info', [app\controller\api\ProductController::class, 'info']);
        Route::get('/product/iap/info', [app\controller\api\ProductController::class, 'iapInfo']);

        Route::get('/app/config', [app\controller\api\AppController::class, 'config']);
        Route::get('/app/upgrade', [app\controller\api\AppController::class, 'upgrade']);

        // Apple收据验证接口 - 轻量级独立验证
        Route::post('/apple/receipt/verify', [app\controller\api\AppleReceiptController::class, 'verify']);
    })->middleware([app\middleware\ApiSignCheck::class]);
});




