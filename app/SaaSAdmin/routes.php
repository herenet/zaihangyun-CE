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
    $router->resource('app/manager/{app_key}/user', 'Manager\UserController')->names('app.manager.user');
});
