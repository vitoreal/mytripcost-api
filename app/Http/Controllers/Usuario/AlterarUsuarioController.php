<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use App\Models\Status;
use App\Models\User;
use App\Repositories\EnderecoRepository;
use App\Repositories\StatusRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class AlterarUsuarioController extends Controller
{

    public function __construct(User $usuario, Endereco $endereco, Status $status){
        $this->usuario = $usuario;
        $this->endereco = $endereco;
        $this->status = $status;
    }

    public function __invoke(Request $request){

        try {

            $user = auth()->userOrFail();

            $validator = Validator::make($request->all(), [
                'status' => $request->status != '' ? 'required' : '',
                'nome' => 'required|string|between:2,100',
                'telefone' => 'required|string',
                'cidade' => 'required',
                'estado' => 'required',
                'cep' => 'required|string',
                'bairro' => 'required|string|between:2,100',
                'endereco' => 'required|string|between:2,200',
                'numero' => 'required|string|between:0,10',
                'complemento' => $request->complemento != '' ? 'string|between:0,200' : '',
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!', 'error-msg' => $validator->errors() ], Response::HTTP_BAD_REQUEST);
            }

            // Alterando os dados do usuario
            $usuarioRepo = new UsuarioRepository($this->usuario);

            if ($request->id){
                if($user->isAdmin()) {
                    $idUser = $request->id;
                } else {
                    $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                }
            } else {
                $idUser = $user->id;
            }

            $usuario = $usuarioRepo->buscarPorId($idUser); // Busco o usuario do banco para poder atualizar

            if($user->isAdmin() && $request->status){
                $usuario->status_id = $request->status;
            }

            $usuario->name = $request->nome;
            $usuario->telefone = $request->telefone;

            $usuarioRepo->salvar($usuario); // Salvando o usuario

            // Salvando/Alterando os dados do endereco usuario
            $enderecoRepo = new EnderecoRepository($this->endereco);

            $endereco = $enderecoRepo->buscarPorIdUsuario($idUser);

            if($endereco === null) {
                $endereco = new Endereco();
            }

            $endereco->id_cidade = $request->cidade;
            $endereco->cep = $request->cep;
            $endereco->bairro = $request->bairro;
            $endereco->endereco = $request->endereco;
            $endereco->numero = $request->numero;
            $endereco->complemento = $request->complemento;
            $endereco->user_id = $usuario->id;

            $enderecoRepo->salvar($endereco); // Salvando o usuario

            if($enderecoRepo === null){
                $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível alterar o dado!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            } else {

                $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro alterado com sucesso!'];
                return response()->json($retorno, 200);
            }

        } catch (UserNotDefinedException $e) {
            //return response()->json($validator->errors(), 422);
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Usuário não está logado!'], Response::HTTP_BAD_REQUEST);

        }
    }
    
}
