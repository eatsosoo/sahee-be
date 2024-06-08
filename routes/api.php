<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login',[UserController::class,'loginUser']);


Route::group(['middleware' => 'auth:sanctum'],function(){
    Route::get('user',[UserController::class,'userDetails']);
    Route::get('logout',[UserController::class,'logout']);
});

Route::group(['prefix' => 'posts', 'middleware' => []], function () {
    Route::get('/', [BookController::class, 'search']);
    Route::delete('/{id}', [BookController::class, 'delete']);
    Route::post('/', [BookController::class, 'create']);
    Route::put('/', [BookController::class, 'update']);
});

Route::group(['prefix' => 'comments', 'middleware' => []], function () {
    Route::get('/', [CommentController::class, 'search']);
    Route::delete('/{id}', [CommentController::class, 'delete']);
    Route::post('/', [CommentController::class, 'create']);
    Route::put('/', [CommentController::class, 'update']);
});
