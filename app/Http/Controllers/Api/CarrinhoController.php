<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrinho;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * TODO - Pegar o id do usuario pela sessao que n sei onde faz
     */
    public function show(Request $request)
    {


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrinho $carrinho)
    {
        //
    }

    public function deleteOne(Request $request)
    {


    }

    public function deleteAll(Request $request)
    {


    }
}
