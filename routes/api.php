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

Route::prefix('user')->group(function (){

	Route::post('/sign-up', [UserController::class, 'signUpUser']);

	Route::post('/restorePassword', [UserController::class, 'restorePassword']);

	Route::post('/register/card', [UserController::class, 'registerCard']);

    Route::post('/register/collection', [UserController::class, 'registerCollection']);

});

Route::prefix('selling')->group(function (){

    Route::get('/cards/list/{name}', [SellingController::class, 'cardsByName']);
    
});