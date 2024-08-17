<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class AlterarSenhaUsuarioController extends Controller
{

    public function __construct(User $usuario){
        $this->usuario = $usuario;
    }


    public function __invoke(Request $request)
    {

        try {

            $user = auth()->userOrFail();

            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os dados!'], Response::HTTP_BAD_REQUEST);
            }

            $password = Hash::make($request->password);

            // Alterando os dados do usuario
            $usuarioRepo = new UsuarioRepository($this->usuario);
            $usuario = $usuarioRepo->alterarSenha($password, $user->email);

            if($usuario === null){

                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível alterar o dado!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);

            } else {
                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Sua senha foi modificada com sucesso!'],  Response::HTTP_OK);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

}
