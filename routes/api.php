<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
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

Route::get('/user', [UserController::class, 'userDetails']);
Route::post('/register', [UserController::class, 'register']);


Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/is_authorized', [AuthController::class, 'checkBearerToken']);

Route::group(['middleware' => 'auth:sanctum'],function(){
    Route::get('user',[UserController::class,'userDetails']);
});

Route::group(['prefix' => 'books', 'middleware' => []], function () {
    Route::get('/', [BookController::class, 'search']);
    Route::get('/{id}', [BookController::class, 'getBook']);
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

Route::group(['prefix' => 'categories', 'middleware' => []], function () {
    Route::get('/', [CategoryController::class, 'search']);
    Route::post('/', [CategoryController::class, 'create']);
    Route::get('/{id}', [CategoryController::class, 'getCategory']);
    Route::delete('/{id}', [CategoryController::class, 'delete']);
    Route::put('/', [CategoryController::class, 'update']);
});

Route::group(['prefix' => 'orders', 'middleware' => []], function () {
    Route::get('/', [OrderController::class, 'search']);
    Route::post('/', [OrderController::class, 'create']);
    Route::get('/{id}', [OrderController::class, 'getOrder']);
    Route::delete('/{id}', [OrderController::class, 'delete']);
    Route::put('/', [OrderController::class, 'update']);
});
