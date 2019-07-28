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

Route::get('/', 'HomeController@start');


Route::get('/admin', 'AdminController@index')->name('admin_home');


Auth::routes();

Route::post('/admin/logout', 'AdminController@logout')->name('admin_logout');
Route::get('/home', 'HomeController@index')->name('home');
// For saving connected user into stripe and database
Route::get('/connect', 'StripeController@connect')->name('connected');
// Charge User
Route::post('/stripe/charge', 'StripeController@charge')->name('charge');
Route::post('/admin/assign','AdminController@assign')->name('assign');