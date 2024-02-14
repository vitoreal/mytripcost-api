<?php

namespace App\Http\Controllers;

use App\Models\Moeda;
use App\Repositories\MoedaRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MoedaController extends Controller
{
    public function __construct(Moeda $moeda){
        $this->moeda = $moeda;
    }

    public function listarMoeda()
    {

        $repository = new MoedaRepository($this->moeda);

        $repository->findAll();
        $moeda = array();

        if($repository){

            $moeda = $repository->model;
            return response()->json($moeda, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar os estados!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
