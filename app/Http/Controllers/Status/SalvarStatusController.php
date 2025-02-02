<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class SalvarStatusController extends Controller
{

    public function __construct(Status $status){
        $this->status = $status;
    }

    public function __invoke(Request $request){

        try {

            $user = auth()->userOrFail();

            if($user->isRoot()) {

                $validator = Validator::make($request->all(), [
                    'nome' => 'required|string|between:5,50',
                ]);

                if ($validator->fails()) {
                    return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!'], Response::HTTP_BAD_REQUEST);
                }

                // Alterando os dados do usuario
                $statusRepo = new StatusRepository($this->status);

                $total = $statusRepo->verificarNomeExiste($request->nome);

                if($total > 0){
                    $retorno = ['type' => 'WARNING', 'mensagem' => 'Este registro já existe!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }

                $acao = ['cadastrado', 'cadastrar'];

                if($request->id){

                    $status = $statusRepo->buscarPorId($request->id);
                    $acao = ['alterado', 'alterar'];

                } else {
                    $status = new Status();
                }

                $status->nome = $request->nome;

                $statusRepo->salvar($status);

                if($statusRepo === null){
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
