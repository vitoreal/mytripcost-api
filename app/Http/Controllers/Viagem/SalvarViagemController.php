<?php

namespace App\Http\Controllers\Viagem;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use Illuminate\Http\Request;
use App\Models\Viagem;
use App\Repositories\ViagemRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class SalvarViagemController extends Controller
{

    public function __construct(Viagem $viagem, FotoViagem $fotoViagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
        $this->diretorio = $diretorio;
    }

    public function __invoke(Request $request){

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
                if($user->roles[0]->name == RolesEnum::USER_BASICO){

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

}
