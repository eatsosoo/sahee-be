<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
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
});
