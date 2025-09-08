<?php

use Illuminate\Support\Facades\Route;
use App\Admin\Controllers\ShowController;
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

Route::get('/', function(){
    return redirect('console');
});

Route::get('/article/{app_key}/{id}', [ShowController::class, 'show'])->name('article.show');
Route::get('/article/category/{app_key}/{id}', [ShowController::class, 'category'])->name('article.category.show');
Route::get('/article/category/{app_key}/{id}/load', [ShowController::class, 'category'])->name('article.category.load');