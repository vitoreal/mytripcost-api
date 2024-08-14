<?php

namespace App\Http\Controllers\MetodoPagamento;

use App\Http\Controllers\Controller;
use App\Models\MetodoPagamento;
use App\Repositories\MetodoPagamentoRepository;
use Illuminate\Http\Response;

class ListarMetodoPagamentoController extends Controller
{
    public function __construct(MetodoPagamento $metodoPagamento){
        $this->metodoPagamento = $metodoPagamento;
    }

    public function __invoke()
    {

        $repository = new MetodoPagamentoRepository($this->metodoPagamento);

        $repository->findAll();
        $metodoPagamento = array();

        if($repository){

            $metodoPagamento = $repository->model;
            return response()->json($metodoPagamento, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar os métodos de pagamento!'], Response::HTTP_BAD_REQUEST);
        }
    }
}
