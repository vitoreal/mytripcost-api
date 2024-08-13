<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutAuthController extends Controller
{

    public function __invoke(){

        Auth::logout();
        /*
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });
        */

        $retorno = [
            'type' => 'SUCESSO',
            'mensagem' => 'Logout efetuado com sucesso!',
        ];

        return response()->json($retorno, 200);

    }

}
