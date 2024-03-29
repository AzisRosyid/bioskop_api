<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SeatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('refresh', [UserController::class, 'refreshToken']);
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::get('movie', [MovieController::class, 'index']);
Route::post('movie', [MovieController::class, 'store']);
Route::get('movie/{id}', [MovieController::class, 'show']);
Route::get('seat', [SeatController::class, 'index']);
Route::get('ticket', [OrderController::class, 'index']);
Route::post('order', [OrderController::class, 'store']);
Route::post('order/detail', [OrderController::class, 'storeDetail']);

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'
], function ($router) {

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
