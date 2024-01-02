<?php

namespace App\Http\Controllers;

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

class UsuarioController extends Controller
{

    public function __construct(User $usuario, Endereco $endereco, Status $status){
        $this->usuario = $usuario;
        $this->endereco = $endereco;
        $this->status = $status;
    }

    /**
     * Alterar dados do usuario
     */
    public function alterarDados(Request $request){

        try {

            $user = auth()->userOrFail();

            $validator = Validator::make($request->all(), [
                'status' => $request->status != '' ? 'required' : '',
                'nome' => 'required|string|between:2,100',
                'telefone' => 'required|string',
                'cidade' => 'required|string',
                'estado' => 'required|string',
                'cep' => 'required|string',
                'bairro' => 'required|string|between:2,100',
                'endereco' => 'required|string|between:2,200',
                'numero' => 'required|string|between:0,10',
                'complemento' => $request->complemento != '' ? 'string|between:0,200' : '',
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!'], Response::HTTP_BAD_REQUEST);
            }

            // Alterando os dados do usuario
            $usuarioRepo = new UsuarioRepository($this->usuario);

            if ($request->id){
                if($user->isAdmin() || $user->isRoot()) {
                    $idUser = $request->id;
                } else {
                    $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                }
            } else {
                $idUser = $user->id;
            }

            $usuario = $usuarioRepo->buscarPorId($idUser); // Busco o usuario do banco para poder atualizar

            if(($user->isAdmin() || $user->isRoot()) && $request->status){
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

    public function buscarMeusDados(){

        $user = Auth::user();

        $usuario = $this->getUserById($user->id);

        $retorno = [
            'user' => $usuario
        ];

        return response()->json($retorno, Response::HTTP_OK);

    }

    // Busca usuario por id - somente admin e root tem acesso
    public function buscarUsuario(int $idUser){

        try{

            $user = Auth::user();

            if($user->isAdmin() || $user->isRoot()) {

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

    public function getUserById($idUser){

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

    public function listarPagination( string $startRow, string $limit, string $sortBy){
        try {
            $user = Auth::user();

            if($user->isAdmin() || $user->isRoot()) {
                // Alterando os dados do usuario
                $usuarioRepo = new UsuarioRepository($this->usuario);

                $lista = $usuarioRepo->listarPagination($startRow, $limit, $sortBy, 'name');

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

    public function listarTotalPagination(){

        try {

            $user = auth()->userOrFail();

            //$filter = $request->query('filter');
            if($user->isAdmin() || $user->isRoot()) {

                // Alterando os dados do usuario
                $usuarioRepo = new UsuarioRepository($this->usuario);

                $total = $usuarioRepo->listarTotalPagination();

                $retorno = [
                    'total' => $total
                ];

                return response()->json($retorno, Response::HTTP_OK);
            } else {

                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }


    /**
     * Metodo para alterar a senha
     *
     * @return response()
     */
    public function alterarSenha(Request $request)
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

    /**
     * Metodo para alterar a senha
     *
     * @return response()
     */
    public function excluir(Request $request)
    {

        try {

            $user = Auth::userOrFail();

            if($user->isAdmin() || $user->isRoot()) {

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

    /**
     * Metodo para alterar a senha
     *
     * @return response()
     */
    public function excluirConta()
    {

      try {
            $user = Auth::userOrFail();

            Auth::user()->tokens->each(function($token, $key) {
                $token->delete();
            });

            $user = User::find($user->id);
            $user->delete();

            return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Usuário deletado com sucesso! Você não poderá mais acessar o sistema.'], 200);

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }
}
