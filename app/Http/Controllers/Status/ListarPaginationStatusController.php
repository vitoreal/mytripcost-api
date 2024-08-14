<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class ListarPaginationStatusController extends Controller
{

    public function __construct(Status $status){
        $this->status = $status;
    }

    public function __invoke( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            if($user->isAdmin()) {

                $statusRepo = new StatusRepository($this->status);

                $lista = $statusRepo->listarPagination($startRow, $limit, $sortBy, 'id');

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

}
