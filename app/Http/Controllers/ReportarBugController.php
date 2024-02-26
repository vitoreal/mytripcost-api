<?php

namespace App\Http\Controllers;

use App\Models\ReportarBug;
use App\Repositories\ReportarBugRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ReportarBugController extends Controller
{
    public function __construct(ReportarBug $reportarBug){
        $this->reportarBug = $reportarBug;
    }

    /**
     * Alterar dados do usuario
     */
    public function salvar(Request $request){

        try {

            $user = auth()->userOrFail();


            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|between:5,500',
                'descricao' => 'required|string|between:5,8000',
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!'], Response::HTTP_BAD_REQUEST);
            }

            // Alterando os dados do usuario
            $repository = new ReportarBugRepository($this->reportarBug);

            $reportarBug = new ReportarBug();

            $reportarBug->titulo = $request->titulo;
            $reportarBug->descricao = $request->descricao;
            $reportarBug->user_id = $user->id;

            if($request->foto){
                //$file = base64_encode(file_get_contents($request->foto->path()));

                $path = $request->file('foto')->store(
                    'images/foto-reportar-bug/'.$user->id
                );

                $reportarBug->foto = $path;
            }

            $repository->salvar($reportarBug);
/*
            $file_extension = $file->getClientOriginalName();
            $destination_path = public_path() . '/folder/images/';
            $filename = $file_extension;
            $file->move($destination_path, $filename);
*/
            if($repository === null){
                $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível cadastrar o dado!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            } else {
                $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro cadastrado com sucesso!'];
                return response()->json($retorno, Response::HTTP_OK);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

    // Busca metodoPagamento por id - somente admin e root tem acesso
    public function buscarPorId(int $id){

        try {

            $user = auth()->userOrFail();

            if($user->isRoot()) {

                $repository = new ReportarBugRepository($this->reportarBug);

                $result = $repository->buscarPorId($id);

                //$base64 = base64_decode($result->foto);
                //$result->foto = $base64;

                $retorno = [
                    'result' => $result
                ];

                return response()->json($retorno, 200);
            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

    public function listarPagination( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            if($user->isRoot()) {

                $repository = new ReportarBugRepository($this->reportarBug);

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

            if($user->isRoot()) {

                $repository = new ReportarBugRepository($this->reportarBug);

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
     * Metodo para excluir
     *
     * @return response()
     */
    public function excluir(Request $request)
    {

        try {

            $user = Auth::userOrFail();

            if($user->isRoot()) {

                $repository = new ReportarBugRepository($this->reportarBug);

                $repository->excluir($request->id);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }


    }
}
