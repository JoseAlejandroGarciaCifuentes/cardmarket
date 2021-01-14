<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;

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

Route::prefix('users')->group(function (){

    Route::post('/signup', [UserController::class, 'signUp']);
    
    Route::post('/login', [UserController::class, 'login']);

	Route::post('/restorePassword', [UserController::class, 'restorePassword']);

    Route::post('/register/collection', [UserController::class, 'registerCollection']);

});

Route::prefix('cards')->group(function (){

    Route::get('/selling/list/{name}', [CardController::class, 'sellingsByPrice']);

    Route::post('/register/card', [CardController::class, 'registerCard']);
    
    Route::get('/all/{name}', [CardController::class, 'cardsByName']);

});

Route::prefix('collections')->group(function (){

    Route::post('/assign/card', [CollectionController::class, 'assignCard']);

});
