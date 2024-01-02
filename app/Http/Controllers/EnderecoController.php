<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Cidade;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\EnderecoRepository;
use App\Repositories\EstadoRepository;
use App\Repositories\CidadeRepository;

class EnderecoController extends Controller
{
    public function __construct(Endereco $endereco, Cidade $cidade, Estado $estado){
        $this->endereco = $endereco;
        $this->cidade = $cidade;
        $this->estado = $estado;
    }
    
    public function listaEstado()
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

    /**
     * Display a listing of the resource.
     */
    public function listaCidade(Request $request)
    {
        
        $cidadeRepo = new CidadeRepository($this->cidade);

        $cidadeRepo->findPorIdEstado($request->idEstado);

        $cidade = array();

        if($cidadeRepo){
            
            $cidade = $cidadeRepo->cidade;
            return response()->json($cidade, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível buscar os estados!'], Response::HTTP_BAD_REQUEST);
        }
    }
    
}
