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

Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);
Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');

Route::get('categories/{category}', 'CategoriesController@show')->name('categories.show');

// 话题图片上传
Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');

Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);

// 通知列表
Route::get('notifications', 'NotificationsController@index')->name('notifications.index');
Route::get('permission-denied', 'PagesController@permissionDenied')->name('permission-denied');
