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

    $router->get('app/manager/{app_key}', 'Manager\IndexController@index')->name('app.manager.index');
    $router->group(['prefix' => 'app/manager/{app_key}'], function($router) {
        $router->resource('config', 'Manager\AppConfigController')->names('app.manager.config');

        $router->resource('user/list', 'Manager\UserController')->names('app.manager.user');
        $router->get('user/config', 'Manager\UserConfigController@index')->name('app.manager.user.config');
        $router->post('user/config/base', 'Manager\UserConfigController@saveBase')->name('app.manager.user.config.save.base');
        $router->post('user/config/sms', 'Manager\UserConfigController@saveSms')->name('app.manager.user.config.save.sms');
        $router->post('user/config/sms/check-interface', 'Manager\UserConfigController@checkSmsInterface')->name('app.manager.user.config.check.sms.interface');
        $router->post('user/config/wechat', 'Manager\UserConfigController@saveWechat')->name('app.manager.user.config.save.wechat');

        $router->resource('order/list', 'Manager\OrderController')->names('app.manager.order');
        $router->get('order/config', 'Manager\OrderConfigController@index')->name('app.manager.order.config');
        $router->post('order/config/base', 'Manager\OrderConfigController@saveBase')->name('app.manager.order.config.save.base');
        $router->resource('order/product', 'Manager\OrderProductController')->names('app.manager.order.product');
        $router->post('order/config/wechat', 'Manager\OrderConfigController@saveWechat')->name('app.manager.order.config.save.wechat');
        $router->post('order/config/alipay', 'Manager\OrderConfigController@saveAlipay')->name('app.manager.order.config.save.alipay');
        $router->post('order/config/alipay/check-interface', 'Manager\OrderConfigController@checkAlipayInterface')->name('app.manager.order.config.check.alipay.interface');
        
        $router->resource('help/list', 'Manager\ArticleController')->names('app.manager.help');


        $router->resource('agreement/list', 'Manager\AgreementController')->names('app.manager.agreement');


        $router->get('api_tester', 'Manager\ZaihangyunApiTesterController@index')->name('api.tester');
        $router->post('api_tester/handle', 'Manager\ZaihangyunApiTesterController@handle')->name('api.tester.handle');
    });
});
