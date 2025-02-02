<?php

namespace App\Http\Controllers\Despesa;

use App\Enums\MetodoPagamentoEnum;
use App\Http\Controllers\Controller;
use App\Models\Despesa;
use App\Models\Viagem;
use App\Repositories\DespesaRepository;
use App\Repositories\ViagemRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class SalvarDespesaController extends Controller
{
    public function __construct(Despesa $despesa, Viagem $viagem){
        $this->viagem = $viagem;
        $this->despesa = $despesa;
    }

    /**
     * Salvar dados
     */
    public function __invoke(Request $request){

        try {

            $user = auth()->userOrFail();

            $rules = [
                'moeda' => 'required',
                'nome' => 'required|string|max:100',
                'categoria' => 'required',
                'valor' => 'string|max:1000',
                'dataDespesa' => 'date_format:Y-m-d',
                'idViagem' => 'required',
            ];

            $messages = [
                'moeda.required' => 'Campo moeda é o obrigatório',
                'categoria.required' => 'Campo categoria é o obrigatório',
                'nome.required' => 'Campo nome é o obrigatório',
                'nome.max' => 'Campo nome não pode ultrapassar de 100 caracteres',
                'valor.max' => 'Campo valor muito grande',
                'dataDespesa.date_format' => 'Formato de data inválido',
                'idViagem' => 'Algum problema ocorreu - Error Viagem',
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

            $repositoryViagem = new ViagemRepository($this->viagem);

            $viagem = $repositoryViagem->buscarPorId($request->idViagem);

            if($viagem){

                // Verificando se é o proprio usuario da viagem que está cadastrando a despesa
                if($viagem->id != $request->idViagem && $viagem->user_id != $user->id){
                    $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage()];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                }

                $repository = new DespesaRepository($this->despesa);

                $idDespesa = 0; // Flag para verificar se é um cadastro novo

                if($request->id){
                    $idDespesa = $request->id;
                }

                if(MetodoPagamentoEnum::ID_OUTROS != $request->metodoPagamento){
                    $request->outrosMetodoPagamento = "";
                }

                $total = $repository->verificarNomeExiste($request->nome, $request->idViagem, $idDespesa);

                if($total > 0){
                    $retorno = ['type' => 'WARNING', 'mensagem' => 'Já existe uma despesa com esse nome!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }

                $acao = ['cadastrado', 'cadastrar'];

                if($request->id){

                    $despesa = $repository->buscarPorId($request->id);
                    $acao = ['alterado', 'alterar'];

                } else {
                    $despesa = new Despesa();
                }

                $valor = str_replace('.','', $request->valor);
                $valor = str_replace(',','.', $valor);

                $despesa->nome = $request->nome;
                $despesa->id_viagem = $request->idViagem;
                $despesa->data_despesa = $request->dataDespesa;
                $despesa->id_moeda = $request->moeda;
                $despesa->id_categoria = $request->categoria;
                $despesa->valor = $valor;
                $despesa->id_metodo_pagamento = $request->metodoPagamento;
                $despesa->outros_metodo_pagamento = $request->outrosMetodoPagamento;

                $repository->salvar($despesa);

                if($repository === null){
                    $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível '.$acao[1].' o dado!' ];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                } else {

                    $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro '.$acao[0].' com sucesso!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }
            }

        } catch (UserNotDefinedException | QueryException | Exception $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage()];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

}
