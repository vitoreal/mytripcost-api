<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RefreshTokenAuthController extends Controller
{


    public function __invoke(){

        $token = Auth::refresh();

        $retorno = [
            'type' => 'SUCESSO',
            'mensagem' => 'Token atualizado com sucesso!',
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer'
            ]
        ];

        return response()->json($retorno, 200);
    }

}
