<?php

namespace App\Http\Controllers\Categoria;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use PDOException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;


class ListarPaginationCategoriaController extends Controller
{

    public function __construct(Categoria $categoria){
        $this->categoria = $categoria;
    }

    public function __invoke( string $startRow, string $limit, string $sortBy){

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

        } catch (UserNotDefinedException | UnauthorizedHttpException | PDOException | QueryException | Throwable | Exception $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

}
