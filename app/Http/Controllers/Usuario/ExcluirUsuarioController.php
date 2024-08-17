<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use App\Models\Status;
use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class ExcluirUsuarioController extends Controller
{

    public function __construct(User $usuario, Endereco $endereco, Status $status){
        $this->usuario = $usuario;
        $this->endereco = $endereco;
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

            if($user->isAdmin()) {

                // excluindo os dados do usuario
                $usuarioRepo = new UsuarioRepository($this->usuario);
                $usuarioRepo->excluir($request->id);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Usuário deletado com sucesso! Este usuário não poderá mais acessar o sistema.'], 200);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (Throwable $e) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }



    }

}
