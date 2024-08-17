<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\User;
use App\Repositories\StatusRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class BuscarUsuarioController extends Controller
{

    public function __construct(User $usuario, Status $status){
        $this->usuario = $usuario;
        $this->status = $status;
    }

    public function __invoke(int $idUser){

        try{

            $user = Auth::user();

            if($user->isAdmin()) {

                $usuario = $this->getUserById($idUser);

                $statusRepo = new StatusRepository($this->status);

                $listaStatus = $statusRepo->findAll();

                $retorno = [
                    'user' => $usuario,
                    'listaStatus' => $listaStatus
                ];

                return response()->json($retorno, Response::HTTP_OK);
            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!' ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!'.$e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }
    }

    private function getUserById($idUser){

        // Alterando os dados do usuario
        $usuarioRepo = new UsuarioRepository($this->usuario);

        $usuario = $usuarioRepo->buscarPorId($idUser);
        $usuario->status;

        if($usuario->endereco){
            $usuario->endereco;
            $usuario->endereco->cidade;
            $usuario->endereco->cidade->estado;
        }

        return $usuario;

    }

}
