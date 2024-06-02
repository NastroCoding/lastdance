<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::prefix('v1')->group(function() {
    Route::controller(AuthController::class)->prefix('auth')->group(function() {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
    });
    Route::middleware('auth:sanctum')->group(function() {
        Route::controller(AuthController::class)->prefix('auth')->group(function() {
            Route::post('/logout', 'logout');
        });
        Route::controller(PostController::class)->group(function() {
            Route::post('/posts', 'store');
            Route::delete('/posts/{id}', 'destroy');
            Route::get('/posts', 'index');
        });
        Route::controller(UserController::class)->group(function() {
            Route::get('/users', 'index');
            Route::get('/users/{username}', 'show');
        });
        Route::controller(FollowController::class)->group(function(){
            Route::post('/users/{username}/follow', 'follow');
            Route::delete('/users/{username}/unfollow', 'unfollow');
            Route::get('/users/{username}/following', 'following');
            Route::get('/users/{username}/followers', 'followers');
        });
    });
});