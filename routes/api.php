<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QRCodeController;
use App\Models\Permission;
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

// Route::get('/user', [UserController::class, 'userDetails']);
Route::post('/register', [UserController::class, 'register']);


Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/is_authorized', [AuthController::class, 'checkBearerToken']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [UserController::class, 'userDetails']);
});

Route::group(['prefix' => 'books', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [BookController::class, 'search'])->middleware('api.auth.gate:' . Permission::PRODUCT_LIST['id']);
    Route::get('/{id}', [BookController::class, 'getBook'])->middleware('api.auth.gate:' . Permission::PRODUCT_READ['id']);
    Route::delete('/{id}', [BookController::class, 'delete'])->middleware('api.auth.gate:' . Permission::PRODUCT_DELETE['id']);
    Route::post('/', [BookController::class, 'create'])->middleware('api.auth.gate:' . Permission::PRODUCT_CREATE['id']);
    Route::put('/', [BookController::class, 'update'])->middleware('api.auth.gate:' . Permission::PRODUCT_UPDATE['id']);
});

Route::group(['prefix' => 'comments', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [CommentController::class, 'search'])->middleware('api.auth.gate:' . Permission::COMMENT_LIST['id']);
    Route::delete('/{id}', [CommentController::class, 'delete'])->middleware('api.auth.gate:' . Permission::COMMENT_DELETE['id']);
    Route::post('/', [CommentController::class, 'create'])->middleware('api.auth.gate:' . Permission::COMMENT_CREATE['id']);
    Route::put('/', [CommentController::class, 'update'])->middleware('api.auth.gate:' . Permission::COMMENT_UPDATE['id']);
    Route::get('/{id}/rating', [CommentController::class, 'rating'])->middleware('api.auth.gate:' . Permission::COMMENT_READ['id']);
    Route::get('/{order_id}/{book_id}/{user_id}/find_rating', [CommentController::class, 'findComment'])->middleware('api.auth.gate:' . Permission::COMMENT_READ['id']);
});

Route::group(['prefix' => 'categories', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [CategoryController::class, 'search'])->middleware('api.auth.gate:' . Permission::CATEGORY_LIST['id']);
    Route::post('/', [CategoryController::class, 'create'])->middleware('api.auth.gate:' . Permission::CATEGORY_CREATE['id']);
    Route::get('/{id}', [CategoryController::class, 'getCategory'])->middleware('api.auth.gate:' . Permission::CATEGORY_READ['id']);
    Route::delete('/{id}', [CategoryController::class, 'delete'])->middleware('api.auth.gate:' . Permission::CATEGORY_DELETE['id']);
    Route::put('/', [CategoryController::class, 'update'])->middleware('api.auth.gate:' . Permission::CATEGORY_UPDATE['id']);
});

Route::group(['prefix' => 'orders', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [OrderController::class, 'search'])->middleware('api.auth.gate:' . Permission::ORDER_LIST['id']);
    Route::post('/', [OrderController::class, 'create'])->middleware('api.auth.gate:' . Permission::ORDER_CREATE['id']);
    Route::get('/{id}', [OrderController::class, 'getOrder'])->middleware('api.auth.gate:' . Permission::ORDER_READ['id']);
    Route::delete('/{id}', [OrderController::class, 'delete'])->middleware('api.auth.gate:' . Permission::ORDER_DELETE['id']);
    Route::put('/', [OrderController::class, 'update'])->middleware('api.auth.gate:' . Permission::ORDER_UPDATE['id']);
    Route::post('/{id}/status', [OrderController::class, 'updateStatus'])->middleware('api.auth.gate:' . Permission::ORDER_UPDATE['id']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder'])->middleware('api.auth.gate:' . Permission::ORDER_CANCEL['id']);
});