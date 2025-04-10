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
    $router->resource('apps', 'AppController')->names('apps');
    $router->get('apps/list', 'AppController@list')->name('apps.list');
    $router->resource('global/config/wechat/payment', 'WechatPaymentConfigController')->names('global.wechat.payment.config');

    $router->get('app/manager/{app_key}', 'Manager\IndexController@index')->name('app.manager.index');
    $router->group(['prefix' => 'app/manager/{app_key}'], function($router) {
        // $router->post('user/config/save', 'Manager\UserConfigController@save')->name('app.manager.user.config.save');
        // $router->addRoute(['get', 'post'] ,'user/config', 'Manager\UserConfigController@index')->name('app.manager.user.config');
        $router->get('user/config', 'Manager\UserConfigController@index')->name('app.manager.user.config');
        $router->post('user/config/base', 'Manager\UserConfigController@saveBase')->name('app.manager.user.config.save.base');
        $router->post('user/config/sms', 'Manager\UserConfigController@saveSms')->name('app.manager.user.config.save.sms');
        $router->post('user/config/wechat', 'Manager\UserConfigController@saveWechat')->name('app.manager.user.config.save.wechat');

        $router->get('payment/config', 'Manager\OrderConfigController@index')->name('app.manager.payment.config');
        $router->post('payment/config/wechat', 'Manager\OrderConfigController@saveWechat')->name('app.manager.payment.config.save.wechat');

        $router->resource('user', 'Manager\UserController')->names('app.manager.user');
        $router->resource('config/wechat/platform', 'Manager\WechatOpenPlatformConfigController')->names('app.manager.wechat.platform');
        
        $router->post('config/wechat/platform/check-interface', 'Manager\WechatOpenPlatformConfigController@checkInterface')->name('app.manager.wechat.platform.check-interface');
        $router->post('config/wechat/payment/check-interface', 'Manager\WechatPaymentConfigController@checkInterface')->name('app.manager.wechat.payment.check-interface');
    });
});
