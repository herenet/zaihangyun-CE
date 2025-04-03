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

    $router->get('app/manager/{app_key}', 'Manager\IndexController@index')->name('app.manager.index');
    $router->group(['prefix' => 'app/manager/{app_key}'], function($router) {
        // $router->post('user/config/save', 'Manager\UserConfigController@save')->name('app.manager.user.config.save');
        // $router->addRoute(['get', 'post'] ,'user/config', 'Manager\UserConfigController@index')->name('app.manager.user.config');
        $router->get('user/config', 'Manager\UserConfigController@index')->name('app.manager.user.config');
        $router->post('user/config', 'Manager\UserConfigController@save')->name('app.manager.user.config.save');
        $router->resource('user', 'Manager\UserController')->names('app.manager.user');
        $router->resource('config/wechat/platform', 'Manager\WechatOpenPlatformConfigController')->names('app.manager.wechat.platform');
        $router->post('config/wechat/platform/check-interface', 'Manager\WechatOpenPlatformConfigController@checkInterface')->name('app.manager.wechat.platform.check-interface');
    });
});
