<?php

namespace App\Http\Controllers\Endereco;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use Illuminate\Http\Response;
use App\Repositories\EstadoRepository;

class ListaEstadoController extends Controller
{
    public function __construct(Estado $estado){
        $this->estado = $estado;
    }

    public function __invoke()
    {
        $estadoRepo = new EstadoRepository($this->estado);

        $estadoRepo->findAll();
        $estado = array();

        if($estadoRepo){

            $estado = $estadoRepo->model;
            return response()->json($estado, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar os estados!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
