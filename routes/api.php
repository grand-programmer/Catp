<?php

use Illuminate\Http\Request;
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

Route::get('products', 'Api\ProductController@getProducts');
Route::post('products', 'Api\ProductController@createProduct');
Route::put('products/{id}', 'Api\ProductController@updateProduct');
Route::delete('products/{id}','Api\ProductController@deleteProduct');
Route::get('categories', 'Api\CategoryController@getCategorys');
Route::post('categories', 'Api\CategoryController@createCategory');
Route::put('categories/{id}', 'Api\CategoryController@updateCategory');
Route::delete('categories/{id}','Api\CategoryController@deleteCategory');
