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

Route::pattern('url', '.*');
Route::get('/{url?}', 'VueController');

Route::post('success', 'FrontendController@paymentSuccess');
Route::post('fail', 'FrontendController@paymentFailed');
Route::post('cancel', 'FrontendController@paymentCanceled');
Route::post('ipn', 'FrontendController@paymentIpn');
Route::post('save-token', 'FrontendController@saveToken')->name('save-token');
//Route::get('affiliate', 'FrontendController@affiliate');
