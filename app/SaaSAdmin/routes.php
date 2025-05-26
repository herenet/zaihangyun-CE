<?php

use Illuminate\Routing\Router;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', 'AppController@home');

    $router->post('register', 'RegisterController@register');
    $router->post('send-verification', 'RegisterController@sendVerification');
    $router->post('send-login-verification', 'RegisterController@sendLoginVerification');
    $router->post('upload/article/image', 'UploadController@uploadImage')->name('upload.article.image');

    $router->resource('apps', 'AppController')->names('apps');
    $router->get('apps/list', 'AppController@list')->name('apps.list');
    $router->resource('global/config/wechat/payment', 'WechatPaymentConfigController')->names('global.wechat.payment.config');
    $router->post('global/config/wechat/payment/check-interface', 'WechatPaymentConfigController@checkInterface')->name('global.wechat.payment.check-interface');
    $router->resource('global/config/wechat/platform', 'WechatOpenPlatformConfigController')->names('global.wechat.platform.config');
    $router->post('global/config/wechat/platform/check-interface', 'WechatOpenPlatformConfigController@checkInterface')->name('global.wechat.platform.check-interface');
    $router->resource('global/config/aliyun/access', 'AliyunAccessConfigController')->names('global.aliyun.access.config');
    $router->post('global/config/aliyun/access/check-interface', 'AliyunAccessConfigController@checkInterface')->name('global.aliyun.access.check-interface');
    $router->resource('global/config/apple/apicert', 'AppleApiCertConfigController')->names('global.apple.apicert.config');
    $router->post('global/config/apple/apicert/verify', 'AppleApiCertConfigController@verify')->name('global.apple.apicert.verify');

    $router->get('app/manager/{app_key}', 'Manager\IndexController@index')->name('app.manager.index');
    $router->group(['prefix' => 'app/manager/{app_key}'], function($router) {
        $router->resource('config', 'Manager\AppConfigController')->names('app.manager.config');
        $router->get('upgrade', 'Manager\AppUpgradeChannelController@index')->name('app.manager.upgrade');
        $router->post('upgrade', 'Manager\AppUpgradeChannelController@store')->name('app.manager.upgrade.store');
        $router->delete('upgrade/{id}', 'Manager\AppUpgradeChannelController@destroy')->name('app.manager.upgrade.destroy');

        $router->group(['prefix' => 'version/{channel_id}'], function($router) {
            $router->resource('item', 'Manager\AppUpgradeController')->names('app.manager.version');
        });

        $router->resource('user/list', 'Manager\UserController')->names('app.manager.user');
        $router->get('user/config', 'Manager\UserConfigController@index')->name('app.manager.user.config');
        $router->post('user/config/base', 'Manager\UserConfigController@saveBase')->name('app.manager.user.config.save.base');
        $router->post('user/config/sms', 'Manager\UserConfigController@saveSms')->name('app.manager.user.config.save.sms');
        $router->post('user/config/sms/check-interface', 'Manager\UserConfigController@checkSmsInterface')->name('app.manager.user.config.check.sms.interface');
        $router->post('user/config/wechat', 'Manager\UserConfigController@saveWechat')->name('app.manager.user.config.save.wechat');
        $router->post('user/config/apple', 'Manager\UserConfigController@saveApple')->name('app.manager.user.config.save.apple');

        $router->resource('order/list', 'Manager\OrderController')->names('app.manager.order');
        $router->get('order/config', 'Manager\OrderConfigController@index')->name('app.manager.order.config');
        $router->post('order/config/base', 'Manager\OrderConfigController@saveBase')->name('app.manager.order.config.save.base');
        $router->resource('order/product', 'Manager\OrderProductController')->names('app.manager.order.product');
        $router->post('order/config/wechat', 'Manager\OrderConfigController@saveWechat')->name('app.manager.order.config.save.wechat');
        $router->post('order/config/alipay', 'Manager\OrderConfigController@saveAlipay')->name('app.manager.order.config.save.alipay');
        $router->post('order/config/alipay/check-interface', 'Manager\OrderConfigController@checkAlipayInterface')->name('app.manager.order.config.check.alipay.interface');
        $router->post('order/refund/send-code', 'Manager\OrderController@sendRefundCode')->name('app.manager.order.refund.send-code');
        $router->post('order/config/apple/verify-one-time-purchase', 'Manager\OrderConfigController@verifyOneTimePurchase')->name('app.manager.order.config.verify.one-time-purchase');
        $router->post('order/config/apple/verify-notify', 'Manager\OrderConfigController@verifyNotify')->name('app.manager.order.config.verify.notify');
        $router->get('order/config/apple/callback-verify-status', 'Manager\OrderConfigController@getAppleCallbackVerifyStatus')->name('app.manager.order.config.verify.callback-status');
        $router->post('order/config/iap', 'Manager\OrderConfigController@saveIAP')->name('app.manager.order.config.save.iap');
        
        $router->resource('article/category', 'Manager\ArticleCategoryController')->names('app.manager.article.category');
        $router->resource('article/list', 'Manager\ArticleController')->names('app.manager.article.list');
        $router->get('article/config', 'Manager\ArticleConfigController@index')->name('app.manager.article.config');
        $router->post('article/config/base', 'Manager\ArticleConfigController@saveBase')->name('app.manager.article.config.save.base');

        $router->get('message/config', 'Manager\MessageConfigController@index')->name('app.manager.message.config');
        $router->post('message/config/base', 'Manager\MessageConfigController@saveBase')->name('app.manager.message.config.save.base');
        $router->resource('notice/list', 'Manager\NoticeController')->names('app.manager.notice.list');
        $router->resource('feedback/list', 'Manager\FeedbackController')->names('app.manager.feedback.list');
        $router->get('feedback/{id}/reply', 'Manager\FeedbackController@reply')->name('app.manager.feedback.reply');
        $router->post('feedback/{id}/save-reply', 'Manager\FeedbackController@saveReply')->name('app.manager.feedback.save-reply');

        $router->get('api_tester', 'Manager\ZaihangyunApiTesterController@index')->name('api.tester');
        $router->post('api_tester/handle', 'Manager\ZaihangyunApiTesterController@handle')->name('api.tester.handle');
    });
});
