<?php

namespace App\Http\Controllers\Endereco;

use App\Http\Controllers\Controller;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\CidadeRepository;

class ListaCidadeController extends Controller
{
    public function __construct(Cidade $cidade){
        $this->cidade = $cidade;
    }

    public function __invoke(Request $request)
    {

        $cidadeRepo = new CidadeRepository($this->cidade);

        $cidadeRepo->findPorIdEstado($request->idEstado);

        $cidade = array();

        if($cidadeRepo){

            $cidade = $cidadeRepo->model;
            return response()->json($cidade, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar os estados!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
