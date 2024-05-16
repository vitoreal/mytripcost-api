<?php

namespace App\Http\Controllers;

use App\Models\MetodoPagamento;
use App\Repositories\MetodoPagamentoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class MetodoPagamentoController extends Controller
{
    public function __construct(MetodoPagamento $metodoPagamento){
        $this->metodoPagamento = $metodoPagamento;
    }

    /**
     * Alterar dados do usuario
     */
    public function salvar(Request $request){

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

    // Busca por id - somente admin e root tem acesso
    public function buscarPorId(int $id){

        $user = Auth::user();

        if($user->isAdmin()) {

            $repository = new MetodoPagamentoRepository($this->metodoPagamento);

            $result = $repository->buscarPorId($id);

            $retorno = [
                'result' => $result
            ];

            return response()->json($retorno, 200);
        } else {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }
    }

    public function listarPagination( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            if($user->isAdmin()) {

                $repository = new MetodoPagamentoRepository($this->metodoPagamento);

                $lista = $repository->listarPagination($startRow, $limit, $sortBy, 'id');

                $retorno = ['lista' => $lista ];
                return response()->json($retorno, Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (UserNotDefinedException | UnauthorizedHttpException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }

    }

    /**
     * Metodo para alterar a senha
     *
     * @return response()
     */
    public function excluir(Request $request)
    {

        try {

            $user = Auth::userOrFail();

            if($user->isRoot()) {

                $repository = new MetodoPagamentoRepository($this->metodoPagamento);

                $repository->excluir($request->id);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

    public function listarMetodoPagamento()
    {

        $repository = new MetodoPagamentoRepository($this->metodoPagamento);

        $repository->findAll();
        $metodoPagamento = array();

        if($repository){

            $metodoPagamento = $repository->model;
            return response()->json($metodoPagamento, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar os métodos de pagamento!'], Response::HTTP_BAD_REQUEST);
        }
    }
}
