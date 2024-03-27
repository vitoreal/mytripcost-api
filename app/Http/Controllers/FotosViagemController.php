<?php

namespace App\Http\Controllers;

use App\Models\fotos_viagem;
use Illuminate\Http\Request;

class FotosViagemController extends Controller
{
    public function __construct(Viagem $viagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
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
            $viagem->privado = $request->privado;
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
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $request->id];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }
}
