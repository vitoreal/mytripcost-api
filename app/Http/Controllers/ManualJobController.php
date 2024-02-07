<?php

namespace App\Http\Controllers;

use FreeCurrencyApi\FreeCurrencyApi\FreeCurrencyApiClient;
use Illuminate\Http\Request;

class ManualJobController extends Controller
{

    public function executarScheduleMoeda(){

        $currencyapi = new FreeCurrencyApiClient();
        $response = $currencyapi->call();

        if($response === null){
            $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível alterar o dado!'];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        } else {

            $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro alterado com sucesso!'];
            return response()->json($retorno, 200);
        }

    }

}
