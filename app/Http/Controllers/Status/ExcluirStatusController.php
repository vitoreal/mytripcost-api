<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class ExcluirStatusController extends Controller
{

    public function __construct(Status $status){
        $this->status = $status;
    }

    /**
     * Metodo para alterar a senha
     *
     * @return response()
     */
    public function __invoke(Request $request)
    {

        try {

            $user = Auth::userOrFail();

            if($user->isRoot()) {

                $statusRepo = new StatusRepository($this->status);

                $statusRepo->excluir($request->id);

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

}
