<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\Api\CarrinhoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CategoriaController;

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/register', 'register');
    Route::post('auth/login', 'login');
    Route::get('auth/logout', 'logout');

    Route::post('auth/refresh', 'refresh');
    Route::get('auth/user', 'user');

    Route::controller(ProdutoController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/product/{id}', 'show');
        Route::get('/product/search/{name}', 'search');
    });

    Route::controller(CategoriaController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::get('/category/{id}/products', 'showProducts');
    });

    Route::controller(CarrinhoController::class)->group(function () {
        Route::post('/user/cart', 'store');
        Route::get('/user/cart', 'show');
    });
});

        /* 

        Route::patch('/user/{id}/cart', 'update');
        Route::delete('/user/{id}/cart/{id}', 'deleteOne');
         */