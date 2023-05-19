<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrinhoController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\EnderecoController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PedidoController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/register', 'register');
    Route::post('auth/login', 'login');

    Route::middleware('auth:api')->group(function () {
        Route::get('auth/logout', 'logout');
        Route::post('auth/refresh', 'refresh');

        Route::controller(ProdutoController::class)->group(function () {
            Route::get('/products', 'index');
            Route::get('/products/search/{name}', 'search');
            Route::get('/product/{id}', 'show');
        });

        Route::controller(PedidoController::class)->group(function () {
            Route::get('/orders', 'index');
            Route::get('/order/{id}', 'show');
            Route::post('/order', 'store');
        });

        Route::controller(CategoriaController::class)->group(function () {
            Route::get('/categories', 'index');
            Route::get('/category/{id}/products', 'showProducts');
        });

        Route::controller(EnderecoController::class)->group(function () {
            Route::get('/user/addresses', 'index');
            Route::post('/user/address', 'store');
            Route::patch('/user/address/{id}', 'update');
            Route::delete('/user/address/{id}', 'destroy');
        });

        Route::controller(PerfilController::class)->group(function () {
            Route::get('/user', 'show');
            Route::patch('/user', 'update');
        });

        Route::controller(CarrinhoController::class)->group(function () {
            Route::get('/user/cart', 'show');
            Route::post('/user/cart', 'store');
            Route::patch('/user/cart', 'update');
        });
    });
});
