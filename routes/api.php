<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CategoriaController;

Route::controller(ProdutoController::class)->group(function() {
    Route::get('/products', 'index');
    Route::get('/product/{id}', 'show');
});