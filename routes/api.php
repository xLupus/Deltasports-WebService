<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\PerfilController;
use Illuminate\Support\Facades\Route;

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

    Route::controller(PerfilController::class)->group(function () {
        Route::get('/user', 'show');
    });
});
