<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;


class CategoriaController extends Controller
{

    public function __construct(Categoria $categoria){
        $this->categoria = $categoria;
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
                $repository = new CategoriaRepository($this->categoria);

                $total = $repository->verificarNomeExiste($request->nome);

                if($total > 0){
                    $retorno = ['type' => 'WARNING', 'mensagem' => 'Este registro já existe!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }

                $acao = ['cadastrado', 'cadastrar'];

                if($request->id){

                    $categoria = $repository->buscarPorId($request->id);
                    $acao = ['alterado', 'alterar'];

                } else {
                    $categoria = new Categoria();
                }

                $categoria->nome = $request->nome;

                $repository->salvar($categoria);

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

            $repository = new CategoriaRepository($this->categoria);

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

                $repository = new CategoriaRepository($this->categoria);

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

    public function listarTotalPagination(){

        try {

            $user = auth()->userOrFail();

            if($user->isAdmin()) {

                $repository = new CategoriaRepository($this->categoria);

                $total = $repository->listarTotalPagination();

                $retorno = [
                    'total' => $total
                ];

                return response()->json($retorno, Response::HTTP_OK);
            } else {

                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
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

                $repository = new CategoriaRepository($this->categoria);

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

    public function listarCategoria()
    {

        $repository = new CategoriaRepository($this->categoria);

        $repository->findAll();
        $categoria = array();

        if($repository){

            $categoria = $repository->model;
            return response()->json($categoria, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar as categorias!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
