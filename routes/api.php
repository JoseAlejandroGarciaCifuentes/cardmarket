<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SellingController;

use App\Http\Middleware\AuthAdmin;
use App\Http\Middleware\AuthNonAdmin;
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

Route::prefix('users')->group(function (){

    Route::post('/signup', [UserController::class, 'signUp']);
    
    Route::post('/login', [UserController::class, 'login']);

    Route::post('/restorePassword', [UserController::class, 'restorePassword']);
    
    Route::post('/make/admin/{id}/{token}', [UserController::class, 'makeAdmin'])->middleware(AuthAdmin::class);
});

Route::prefix('cards')->group(function (){

    Route::get('/selling/list/{name}', [CardController::class, 'sellingsByPrice']);

    Route::post('/register/new-card/{token}', [CardController::class, 'registerCard']);
    
    Route::get('/all/{name}', [CardController::class, 'cardsByName']);

});

Route::prefix('collections')->group(function (){

    Route::post('/assign/card/{token}', [CollectionController::class, 'assignCard'])->middleware(AuthAdmin::class);

    Route::post('/edit/{id}/{token}', [CollectionController::class, 'editCollection'])->middleware(AuthAdmin::class);

    Route::post('/register/new-collection/{token}', [CollectionController::class, 'registerCollection'])->middleware(AuthAdmin::class);

});

Route::prefix('sellings')->group(function (){

    Route::post('/put-to-sell/{id}/{token}', [SellingController::class, 'startSelling'])->middleware(AuthNonAdmin::class);;

});
