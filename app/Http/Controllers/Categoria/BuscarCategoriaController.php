<?php

namespace App\Http\Controllers\Categoria;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;


class BuscarCategoriaController extends Controller
{

    public function __construct(Categoria $categoria){
        $this->categoria = $categoria;
    }

    // Busca por id - somente admin e root tem acesso
    public function __invoke(int $id){

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

}
