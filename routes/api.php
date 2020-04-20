<?php

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

Route::get('items', 'API\ItemController@index');

Route::post('items', 'API\ItemController@store');

Route::patch('items/{item}', 'API\ItemController@update');

Route::delete('items/{item}', 'API\ItemController@delete');

Route::patch('items/{item}/position', 'API\ItemController@updatePosition');
