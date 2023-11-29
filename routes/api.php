<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TypeController;
use App\Http\Middleware\JwtMiddleware;
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

Route::controller(ApiController::class)->group(function() {
    Route::post('/login', 'authenticate');
    Route::post('/cadastrar', 'register');
});

Route::group(['middleware' => JwtMiddleware::class], function() {
    Route::get('/logout', [ApiController::class, 'logout']);

    Route::get('/products/product/', [ProductController::class,'index']);
    Route::post('/products/product/', [ProductController::class,'store']);
    Route::get('/products/product/{id}/', [ProductController::class,'show']);
    Route::put('/products/product/{id}/', [ProductController::class,'update']);
    Route::delete('/products/product/{id}/', [ProductController::class,'destroy']);
    Route::put('/products/product/{id}/restore/', [ProductController::class,'restore']);

    Route::get('/products/type/', [TypeController::class,'index']);
    Route::post('/products/type/', [TypeController::class,'store']);
    Route::get('/products/type/{id}/', [TypeController::class,'show']);
    Route::put('/products/type/{id}/', [TypeController::class,'update']);
    Route::delete('/products/type/{id}/', [TypeController::class,'destroy']);
    Route::put('/products/type/{id}/restore/', [TypeController::class,'restore']);

    Route::get('/products/brand/', [BrandController::class,'index']);
    Route::post('/products/brand/', [BrandController::class,'store']);
    Route::get('/products/brand/{id}/', [BrandController::class,'show']);
    Route::put('/products/brand/{id}/', [BrandController::class,'update']);
    Route::delete('/products/brand/{id}/', [BrandController::class,'destroy']);
    Route::put('/products/brand/{id}/restore/', [BrandController::class,'restore']);

});