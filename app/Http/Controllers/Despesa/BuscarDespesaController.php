<?php

namespace App\Http\Controllers\Despesa;

use App\Http\Controllers\Controller;
use App\Models\Despesa;
use App\Models\Viagem;
use App\Repositories\DespesaRepository;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class BuscarDespesaController extends Controller
{
    public function __construct(Despesa $despesa, Viagem $viagem){
        $this->viagem = $viagem;
        $this->despesa = $despesa;
    }

  
    public function __invoke(int $id){

        try {

            $user = auth()->userOrFail();

            $repository = new DespesaRepository($this->despesa);

            $result = $repository->buscarPorId($id);

            if($result){

                $viagem = $result->viagem;

                // Verificando se é o proprio usuario da viagem que está buscando a despesa
                if($viagem->user_id != $user->id){
                    $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!'];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                }

                $retorno = [
                    'result' => $result
                ];

                return response()->json($retorno, Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }



        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $result ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }
    }

}
