<?php

namespace App\Http\Controllers\MetodoPagamento;

use App\Http\Controllers\Controller;
use App\Models\MetodoPagamento;
use App\Repositories\MetodoPagamentoRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BuscarMetodoPagamentoController extends Controller
{
    public function __construct(MetodoPagamento $metodoPagamento){
        $this->metodoPagamento = $metodoPagamento;
    }

   
    // Busca por id - somente admin e root tem acesso
    public function __invoke(int $id){

        $user = Auth::user();

        if($user->isAdmin()) {

            $repository = new MetodoPagamentoRepository($this->metodoPagamento);

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

}
