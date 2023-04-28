<?php

namespace App\Traits;

//Exceptions
trait Exception {
    public function exceptions($err) {
        switch (get_class($err)) {
            case \Illuminate\Database\Eloquent\ModelNotFoundException::class:
                return response()->json([
                    'status' => 404,
                    'message' => $err->getMessage(),
                    'data' => null
                ], 404);
                break;

            case \Illuminate\Database\QueryException::class:
                return response()->json([
                    'status' => 500,
                    'message' => $err->getMessage(),
                    'data' => null
                ], 500);
                break;

            case \Exception::class:
                return response()->json([
                    'status' => 500,
                    'message' => $err->getMessage(),
                    'data' => null
                ], 500);
                break;

            default:
                return response()->json([
                    'status' => 500,
                    'mensage' => 'Erro ao efetuar a operação',
                    'data' => null
                ], 500);
                break;
        }
    }
}
