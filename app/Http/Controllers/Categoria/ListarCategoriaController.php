<?php

namespace App\Http\Controllers\Categoria;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Illuminate\Http\Response;


class ListarCategoriaController extends Controller
{

    public function __construct(Categoria $categoria){
        $this->categoria = $categoria;
    }

    public function __invoke()
    {

        $repository = new CategoriaRepository($this->categoria);

        $repository->findAll();
        $categoria = array();

        if($repository){

            $categoria = $repository->model;
            return response()->json($categoria, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar as categorias!'], Response::HTTP_BAD_REQUEST);
        }
    }

}
