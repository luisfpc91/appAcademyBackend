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

Route::post('/upload','imageController@upload');
Route::get('/upload','imageController@get');
Route::delete('/upload','imageController@delete');
Route::put('/upload','imageController@save');

Route::get('/user','userController@get');
Route::post('/user','userController@create');
Route::put('/user','userController@update');
Route::delete('/user','userController@delete');

Route::post('/password','userController@resetpwd');

Route::get('/categories','categoriesController@get');
Route::delete('/categories','categoriesController@delete');
Route::post('/categories','categoriesController@create');
Route::put('/categories','categoriesController@edit');

Route::delete('/login','loginController@logout');
Route::post('/login','loginController@login');

Route::post('/notification','pushController@send');
Route::get('/notification','pushController@getall');

Route::post('/item','itemController@create');
Route::get('/item','itemController@get');
Route::put('/item','itemController@save');
Route::delete('/item','itemController@delete');

Route::get('/fcm','pushController@getfcm');

Route::get('/image/{id}/{w}/{h}','imageController@optimize');

