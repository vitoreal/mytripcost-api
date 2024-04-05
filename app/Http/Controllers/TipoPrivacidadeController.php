<?php

namespace App\Http\Controllers;

use App\Models\TipoPrivacidade;
use App\Repositories\TipoPrivacidadeRepository;
use Illuminate\Http\Response;

class TipoPrivacidadeController extends Controller
{
    public function __construct(TipoPrivacidade $tipoPrivacidade){
        $this->tipoPrivacidade = $tipoPrivacidade;
    }

    public function listar()
    {

        $repository = new TipoPrivacidadeRepository($this->tipoPrivacidade);

        $repository->findAll();
        $tipoPrivacidade = array();

        if($repository){

            $tipoPrivacidade = $repository->model;
            return response()->json($tipoPrivacidade, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a busca!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
