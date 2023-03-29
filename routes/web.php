<?php

use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/produto', [ProdutoController::class, 'index']);
Route::get('/pesquisa', 'ProductController@search')->name('produtos.pesquisa');