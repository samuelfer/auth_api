<?php

use Illuminate\Http\Request;

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


Route::name('api.login')->post('login', 'ApiUsuarioController@login');
Route::post('register', 'ApiUsuarioController@register');
Route::post('refresh', 'ApiUsuarioController@refresh');

 
Route::group(['middleware' => 'auth.jwt','jwt.refresh'], function () {
    Route::get('logout', 'ApiUsuarioController@logout');
 
    Route::get('user', 'ApiUsuarioController@getAuthUser');
 
    Route::get('products', 'ProductController@index');
    Route::get('products/{id}', 'ProductController@show');
    Route::post('products', 'ProductController@store');
    Route::put('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');
});