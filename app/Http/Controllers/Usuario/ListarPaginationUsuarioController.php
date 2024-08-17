<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class ListarPaginationUsuarioController extends Controller
{

    public function __construct(User $usuario){
        $this->usuario = $usuario;
    }

    public function __invoke( string $startRow, string $limit, string $sortBy){
        try {
            $user = Auth::user();

            if($user->isAdmin()) {
                // Alterando os dados do usuario
                $repository = new UsuarioRepository($this->usuario);

                $lista = $repository->listarPagination($startRow, $limit, $sortBy, 'name');

                $retorno = ['lista' => $lista ];
                return response()->json($retorno, Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }
        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!'.$e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

}
