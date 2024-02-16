<?php

namespace App\Http\Controllers;

use App\Models\Viagem;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
class ViagemController extends Controller
{
    public function __construct(Viagem $viagem){
        $this->viagem = $viagem;
    }

    /**
     * Alterar dados do usuario
     */
    public function salvar(Request $request){

        try {

            $user = auth()->userOrFail();

            $rules = [
                'moeda' => 'required',
                'privado' => 'required',
                'nome' => 'required|string|max:100',
                'descricao' => 'string|max:1000',
                'orcamento' => 'required',
                'dataInicio' => 'required|date_format:Y-m-d',
                'dataFim' => 'required|date_format:Y-m-d|after:dataInicio',
            ];

            $messages = [
                'moeda.required' => 'Campo moeda é o obrigatório',
                'privado.required' => 'Campo privado é o obrigatório',
                'nome.required' => 'Campo nome é o obrigatório',
                'nome.max' => 'Campo nome não pode ultrapassar de 100 caracteres',
                'descricao.max' => 'Campo descrição não pode ultrapassar de 1000 caracteres',
                'orcamento.required' => 'Campo orçamento é o obrigatório',
                'dataInicio.date_format' => 'Formato de data inválido',
                'dataInicio.required' => 'Campo data início é o obrigatório',
                'dataFim.date_format' => 'Formato de data inválido',
                'dataFim.required' => 'Formato de data inválido',
                'dataFim.after' => 'O campo data fim tem que ser maior que a da data início',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                $errors = $validator->errors();

                $retorno = [
                            'type' => 'ERROR',
                            'mensagem' => $errors->all()[0],
                            ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

            // Alterando os dados do usuario
            $repository = new ViagemRepository($this->viagem);

            $total = $repository->verificarNomeExiste($request->nome);

            if($total > 0){
                $retorno = ['type' => 'WARNING', 'mensagem' => 'Já existe uma viagem com esse nome!'];
                return response()->json($retorno, Response::HTTP_OK);
            }

            dd($request->all());
            exit;

            $acao = ['cadastrado', 'cadastrar'];

            if($request->id){

                $viagem = $repository->buscarPorId($request->id);
                $acao = ['alterado', 'alterar'];

            } else {
                $viagem = new Viagem();
            }

            $viagem->nome = $request->nome;
            $viagem->data_inicio = $request->dataInicio;
            $viagem->data_fim = $request->dataFim;
            $viagem->orcamento = $request->orcamento;
            $viagem->descricao = $request->descricao;

            if($request->foto){
                $file = base64_encode(file_get_contents($request->foto->path()));
                $viagem->foto = $file;
            }
            $viagem->id_moeda = $request->moeda;
            $viagem->user_id = $user->id;

            $repository->salvar($viagem);

            if($repository === null){
                $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível '.$acao[1].' o dado!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            } else {

                $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro '.$acao[0].' com sucesso!'];
                return response()->json($retorno, Response::HTTP_OK);
            }



        } catch (UserNotDefinedException $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!'];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

    // Busca por id - somente admin e root tem acesso
    public function buscarPorId(int $id){

        $user = Auth::user();

        if($user->isRoot()) {

            $repository = new ViagemRepository($this->viagem);

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

    public function listarPagination( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            if($user->isRoot()) {

                $repository = new ViagemRepository($this->viagem);

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

    public function listarTotalPagination(){

        try {

            $user = auth()->userOrFail();

            if($user->isRoot()) {

                $repository = new ViagemRepository($this->viagem);

                $total = $repository->listarTotalPagination();

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
    public function excluir(Request $request)
    {

        try {

            $user = Auth::userOrFail();

            if($user->isRoot()) {

                $repository = new ViagemRepository($this->viagem);

                $repository->excluir($request->id);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }


    }

}
