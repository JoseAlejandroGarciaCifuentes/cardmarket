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
    
    Route::post('/make/admin/{id}', [UserController::class, 'makeAdmin'])->middleware('AuthAdmin');
    
});

Route::prefix('cards')->group(function (){

    Route::post('/register/new-card', [CardController::class, 'registerCard'])->middleware('AuthAdmin');

    Route::post('/edit/{id}', [CardController::class, 'editCard'])->middleware('AuthAdmin');
    
    Route::get('/all/{name}', [CardController::class, 'cardsByName'])->middleware('AuthNonAdmin');

    Route::get('/selling/list/{name}', [CardController::class, 'sellingsByPrice']);

});

Route::prefix('collections')->group(function (){

    Route::post('/register/new-collection', [CollectionController::class, 'registerCollection'])->middleware('AuthAdmin');

    Route::post('/edit/{id}', [CollectionController::class, 'editCollection'])->middleware('AuthAdmin');

    Route::post('/assign/card', [CollectionController::class, 'assignCard'])->middleware('AuthAdmin');

});

Route::prefix('sellings')->group(function (){

    Route::post('/put-to-sell/{id}', [SellingController::class, 'startSelling'])->middleware('AuthNonAdmin');

});
