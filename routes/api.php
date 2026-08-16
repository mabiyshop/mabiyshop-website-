<?php

use Illuminate\Http\Request;
use App\Http\Controllers\userController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//User
Route::post('/signup', [App\Http\Controllers\userController::class, 'signup'])->name('signup');
Route::post('/signin', [App\Http\Controllers\userController::class, 'signin'])->name('signin');
Route::get('/logout', [App\Http\Controllers\userController::class, 'logout'])->name('logout');
Route::get('/getuser', [App\Http\Controllers\userController::class, 'getuser'])->name('getuser');
Route::get('/checkuser', [App\Http\Controllers\userController::class, 'checkuser'])->name('checkuser');
Route::get('/barcode/{id}', function(Request $request){
    return 'data:image/png;base64,' . DNS1D::getBarcodePNG($request->id, 'C39',5,10);
});

