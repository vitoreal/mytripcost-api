<?php

namespace App\Http\Controllers\Despesa;

use App\Enums\MetodoPagamentoEnum;
use App\Http\Controllers\Controller;
use App\Models\Despesa;
use App\Models\Viagem;
use App\Repositories\DespesaRepository;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class ExcluirDespesaController extends Controller
{
    public function __construct(Despesa $despesa, Viagem $viagem){
        $this->viagem = $viagem;
        $this->despesa = $despesa;
    }

    public function __invoke(Request $request)
    {

        try {

            $user = auth()->userOrFail();

            $repository = new DespesaRepository($this->despesa);

            $idDespesa = $request->id;

            $result = $repository->buscarPorId($idDespesa);
            $viagem = $result->viagem;

            if($viagem){

                // Verificando se é o proprio usuario da viagem que está cadastrando a despesa
                if($viagem->user_id != $user->id){
                    $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage()];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                }

                $repository->excluir($idDespesa);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            }

            return response()->json(['type' => 'ERROR', 'mensagem' => 'Você não tem permissão para deletar esse registro!'], Response::HTTP_BAD_REQUEST);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

}
