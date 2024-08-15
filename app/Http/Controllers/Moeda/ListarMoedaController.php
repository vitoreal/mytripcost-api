<?php

namespace App\Http\Controllers\Moeda;

use App\Http\Controllers\Controller;
use App\Models\Moeda;
use App\Repositories\MoedaRepository;
use Illuminate\Http\Response;

class ListarMoedaController extends Controller
{
    public function __construct(Moeda $moeda){
        $this->moeda = $moeda;
    }

    public function __invoke()
    {
        $repository = new MoedaRepository($this->moeda);

        $repository->findAll();
        $moeda = array();

        if($repository){

            $moeda = $repository->model;
            return response()->json($moeda, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar as moedas!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
