<?php

namespace App\Http\Controllers;

use App\Models\FotoViagem;
use App\Models\Viagem;
use App\Repositories\FotoViagemRepository;
use App\Repositories\ViagemRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class FotosViagemController extends Controller
{
    public function __construct(Viagem $viagem, FotoViagem $fotoViagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
        $this->diretorio = $diretorio;
    }

    /**
     * Alterar dados do usuario
     */
    public function salvar(Request $request){

        try {

            $user = auth()->userOrFail();

            $rules = [
                'foto' => 'required|mimes:jpeg,jpg,png,gif|max:8000',
                'idViagem' => 'required',
            ];

            $messages = [
                'foto.required' => 'Campo foto é o obrigatório',
                'foto.max' => 'Campo foto tem que ter no máximo 8MB',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                $errors = $validator->errors();

                $retorno = [
                            'type' => 'ERROR',
                            'mensagem' => $errors->all()[0],
                            ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

            // Verifica se o id do usuario que esta salvando a foto é o mesmo do usuario da viagem

            $viagemRepo = new ViagemRepository($this->viagem);
            $viagem = $viagemRepo->buscarPorId($request->idViagem);

            if ($viagem->user_id == $user->id){

                $repository = new FotoViagemRepository($this->fotoViagem);

                $fotoViagem = new FotoViagem();

                if($request->foto){

                    $path = $request->file('foto')->store(
                        $this->diretorio.$user->id
                    );

                    $fotoViagem->foto = $path;

                }

                $fotoViagem->id_viagem = $request->idViagem;

                $repository->salvar($fotoViagem);

                if($repository === null){
                    $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível cadastrar o dado!' ];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                } else {

                    $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro cadastrado com sucesso!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }
            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem autorização para realizar essa operação!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }


        } catch (UserNotDefinedException | QueryException | Exception $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $fotoViagem];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

    public function listarPagination( int $idViagem, string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            $repoViagem = new ViagemRepository($this->viagem);
            $viagem = $repoViagem->buscarPorId($idViagem);

            $repository = new FotoViagemRepository($this->fotoViagem);

            if(($viagem->user_id == $user->id) || $user->isAdmin()){
                $lista = $repository->listarPaginationFotoViagem($idViagem, $startRow, $limit, $sortBy, 'id');
                $retorno = ['lista' => $lista ];
                return response()->json($retorno, Response::HTTP_OK);
            }

            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem permissão para essa ação!'];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        } catch (UserNotDefinedException | UnauthorizedHttpException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }

    }

    public function listarTotalPagination(int $idViagem){

        try {

            $user = auth()->userOrFail();

            $repoViagem = new ViagemRepository($this->viagem);
            $viagem = $repoViagem->buscarPorId($idViagem);

            $repository = new FotoViagemRepository($this->fotoViagem);

            $total = 0;

            if(($viagem->user_id == $user->id) || $user->isAdmin()){
                $total = $repository->listarTotalPaginationFotoViagem($idViagem);
                $retorno = ['total' => $total ];
                return response()->json($retorno, Response::HTTP_OK);
            }

            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem permissão para essa ação!'];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
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

            $repository = new ViagemRepository($this->viagem);

            $idViagem = $request->id;

            if($user->isAdmin()) {
                $viagem = $repository->buscarPorId($idViagem);
            } else {
                $viagem = $repository->buscarViagemPorUser($user->id, $idViagem);

            }

            if($viagem){

                if($viagem->foto != ''){
                    Storage::delete($viagem->foto);

                    $diretorio = $this->diretorio.'/'.$user->id;

                    $files = Storage::allFiles($diretorio);

                    if(count($files) == 0){
                        Storage::deleteDirectory($diretorio);
                    }

                }

                $repository->excluir($idViagem);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            }

            return response()->json(['type' => 'ERROR', 'mensagem' => 'Você não tem permissão para deletar esse registro!'], Response::HTTP_BAD_REQUEST);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }
}
