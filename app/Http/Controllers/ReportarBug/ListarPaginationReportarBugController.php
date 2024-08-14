<?php

namespace App\Http\Controllers\ReportarBug;

use App\Http\Controllers\Controller;
use App\Models\ReportarBug;
use App\Repositories\ReportarBugRepository;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class ListarPaginationReportarBugController extends Controller
{
    public function __construct(ReportarBug $reportarBug){
        $this->reportarBug = $reportarBug;
    }

    public function __invoke( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            if($user->isAdmin()) {

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

}
