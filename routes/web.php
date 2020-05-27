<?php

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

Route::get('/', 'PagesController@root')->name('root');

Auth::routes(['verify' => true]);

Route::resource('users', 'UsersController', ['only' => ['show', 'edit', 'update']]);

Route::resource('topics', 'TopicsController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);