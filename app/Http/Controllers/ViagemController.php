<?php

namespace App\Http\Controllers;

use App\Models\FotoViagem;
use Illuminate\Http\Request;
use App\Models\Viagem;
use App\Repositories\FotoViagemRepository;
use App\Repositories\ViagemRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
class ViagemController extends Controller
{

    public function __construct(Viagem $viagem, FotoViagem $fotoViagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
        $this->diretorio = $diretorio;
    }

    /**
     * Alterar dados do usuario
     */
    public function salvar(Request $request){

        try {

            $user = auth()->userOrFail();

            $rules = [
                'moeda' => 'required',
                'tipoPrivacidade' => 'required',
                'nome' => 'required|string|max:100',
                'descricao' => 'string|max:1000',
                'orcamento' => 'required',
                'dataInicio' => 'required|date_format:Y-m-d',
                'dataFim' => 'required|date_format:Y-m-d|after:dataInicio',
            ];

            $messages = [
                'moeda.required' => 'Campo moeda é o obrigatório',
                'tipoPrivacidade.required' => 'Campo privacidade é o obrigatório',
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

            $idViagem = 0; // Flag para verificar se é um cadastro novo

            if($request->id){
                $idViagem = $request->id;
            }

            $total = $repository->verificarNomeExiste($request->nome, $user->id, $idViagem);
            $totalRegistro = $repository->totalRegistroPorIdUser($user->id);

            if($total > 0){
                $retorno = ['type' => 'WARNING', 'mensagem' => 'Já existe uma viagem com esse nome!'];
                return response()->json($retorno, Response::HTTP_OK);
            }

            // Verificando se é o primeiro registro. Todo primeiro registro de viagem tem que ter status = 1 (ATIVO)
            if($totalRegistro > 0){
                if($user->roles[0]->name == 'BASICO'){

                    // Atualizando todas as viagens do usuario mais antigas para o status = 0 (INATIVA)
                    $listaViagens = $repository->buscarRegistroPorIdUser($user->id);

                    if(count($listaViagens)){
                        foreach ($listaViagens as $key => $value) {
                            $listaViagens[$key]->status = 0;
                            $repository->salvar($listaViagens[$key]);
                        }
                    }
                }
            }

            $acao = ['cadastrado', 'cadastrar'];

            if($request->id){

                $viagem = $repository->buscarPorId($request->id);
                $acao = ['alterado', 'alterar'];

                if($request->foto){
                    if($viagem->foto){
                        Storage::delete($viagem->foto);
                    }

                }

            } else {
                $viagem = new Viagem();
            }

            $viagem->nome = $request->nome;
            $viagem->id_tipo_privacidade = $request->tipoPrivacidade;
            $viagem->data_inicio = $request->dataInicio;
            $viagem->data_fim = $request->dataFim;

            $orcamento = str_replace('.','', $request->orcamento);
            $orcamento = str_replace(',','.', $orcamento);

            $viagem->orcamento = $orcamento;
            $viagem->descricao = $request->descricao;

            $viagem->status = 1;

            $novaFoto = "";

            if($request->foto){
                //$file = base64_encode(file_get_contents($request->foto->path()));

                $path = $request->file('foto')->store(
                    $this->diretorio.$user->id
                );

                $viagem->foto = $path;

                $files = Storage::get($path);

                $base64 = base64_encode($files);
                $novaFoto = 'data:image/jpeg;base64,'.$base64;
            }

            $viagem->id_moeda = $request->moeda;
            $viagem->user_id = $user->id;

            $repository->salvar($viagem);

            if($repository === null){
                $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível '.$acao[1].' o dado!' ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            } else {

                $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro '.$acao[0].' com sucesso!', 'foto' => $novaFoto];
                return response()->json($retorno, Response::HTTP_OK);
            }



        } catch (UserNotDefinedException | QueryException | Exception $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage()];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

    // Busca por id - somente admin e root tem acesso
    public function buscarPorId(int $id){

        try {

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            if($user->isAdmin()) {
                $result = $repository->buscarPorId($id);
            } else {
                $result = $repository->buscarViagemPorId($id);
            }

            if($result->foto){
                $files = Storage::get($result->foto);

                $base64 = base64_encode($files);
                $result->foto = 'data:image/jpeg;base64,'.$base64;
            }
            $retorno = [
                'result' => $result
            ];

            return response()->json($retorno, 200);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $result ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }
    }

    public function listarPagination( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            if($user->isAdmin()) {
                $lista = $repository->listarPagination($startRow, $limit, $sortBy, 'id');
            } else {
                $lista = $repository->listarPaginationViagem($startRow, $limit, $sortBy, 'id', $user->id);
            }

            foreach ($lista as $key => $value) {

                $lista[$key]->orcamento = number_format($value->orcamento,2,",",".");;
                $lista[$key]->data_inicio = date("d/m/Y", strtotime($value->data_inicio));
                $lista[$key]->data_fim = date("d/m/Y", strtotime($value->data_fim));



                if($lista[$key]->foto){

                    $files = Storage::get($lista[$key]->foto);

                    $base64 = base64_encode($files);
                    $lista[$key]->foto = 'data:image/jpeg;base64,'.$base64;
                }

            }

            $retorno = ['lista' => $lista ];
            return response()->json($retorno, Response::HTTP_OK);

        } catch (UserNotDefinedException | UnauthorizedHttpException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }

    }

    public function listarTotalPagination(){

        try {

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            if($user->isAdmin()) {
                $total = $repository->listarTotalPagination();
            } else {
                $total = $repository->listarTotalPaginationViagem($user->id);

            }

            $retorno = [
                'total' => $total
            ];

            return response()->json($retorno, Response::HTTP_OK);


        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
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

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            $idViagem = $request->id;

            if($user->isAdmin()) {
                $viagem = $repository->buscarPorId($idViagem);
            } else {
                $viagem = $repository->buscarViagemPorUser($user->id, $idViagem);

            }

            if($viagem){

                $repoFotoViagem = new FotoViagemRepository($this->fotoViagem);

                if($viagem->foto != ''){

                    $fotoViagem = $repoFotoViagem->buscarPorIdViagem($idViagem);

                    if($fotoViagem){
                        foreach ($fotoViagem as $key => $value) {

                            $repoFotoViagem->excluir($value->id);
                            Storage::delete($value->foto);
                        }
                    }

                    Storage::delete($viagem->foto);

                    $diretorio = $this->diretorio.'/'.$user->id;

                    $files = Storage::allFiles($diretorio);

                    if(count($files) == 0){
                        Storage::deleteDirectory($diretorio);
                    }

                }

                $repository->excluir($idViagem);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            }

            return response()->json(['type' => 'ERROR', 'mensagem' => 'Você não tem permissão para deletar esse registro!'], Response::HTTP_BAD_REQUEST);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

}
