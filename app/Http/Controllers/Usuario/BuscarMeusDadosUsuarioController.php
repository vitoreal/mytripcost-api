<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BuscarMeusDadosUsuarioController extends Controller
{

    public function __construct(User $usuario){
        $this->usuario = $usuario;
    }

    public function __invoke(){

        $user = Auth::user();

        $usuario = $this->getUserById($user->id);

        $retorno = [
            'user' => $usuario
        ];

        return response()->json($retorno, Response::HTTP_OK);

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
