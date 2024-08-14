<?php

namespace App\Http\Controllers\MetodoPagamento;

use App\Http\Controllers\Controller;
use App\Models\MetodoPagamento;
use App\Repositories\MetodoPagamentoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class SalvarMetodoPagamentoController extends Controller
{
    public function __construct(MetodoPagamento $metodoPagamento){
        $this->metodoPagamento = $metodoPagamento;
    }

    /**
     * Alterar dados do usuario
     */
    public function __invoke(Request $request){

        try {

            $user = auth()->userOrFail();

            if($user->isRoot()) {

                $validator = Validator::make($request->all(), [
                    'nome' => 'required|string|between:5,100',
                ]);

                if ($validator->fails()) {
                    return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!'], Response::HTTP_BAD_REQUEST);
                }

                // Alterando os dados do usuario
                $repository = new MetodoPagamentoRepository($this->metodoPagamento);

                $total = $repository->verificarNomeExiste($request->nome);

                if($total > 0){
                    $retorno = ['type' => 'WARNING', 'mensagem' => 'Este registro já existe!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }

                $acao = ['cadastrado', 'cadastrar'];
                if($request->id){

                    $metodoPagamento = $repository->buscarPorId($request->id);
                    $acao = ['alterado', 'alterar'];

                } else {
                    $metodoPagamento = new MetodoPagamento();
                }

                $metodoPagamento->nome = $request->nome;

                $repository->salvar($metodoPagamento);

                if($repository === null){
                    $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível '.$acao[1].' o dado!'];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                } else {

                    $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro '.$acao[0].' com sucesso!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }


        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }
}
