<?php

namespace App\Repositories;

class DespesaRepository extends AbstractRepository {

    public function verificarNomeExiste($nome, $idViagem, $idDespesa){
        $total= $this->model->where([['nome', '=', $nome], ['id_viagem', '=', $idViagem], ['id', '!=', $idDespesa]])->count();
        return $total;
    }

    public function buscarRegistroPorIdViagem($id){
        $this->model = $this->model->where('id_viagem', $id)->get();
        return $this->model;
    }

    public function listarTotalPaginationDespesaViagem($idViagem){

        $total = $this->model->where([['id_viagem','=', $idViagem]])->count();

        return  $total;

    }

    public function listarPaginationDespesaViagem($idViagem, $startRow, $limit, $sortBy, $orderBy){

        if($sortBy == ''){
            $sortBy = 'asc';
        }
        if($startRow == ''){
            $startRow = 1;
        }
        if($limit == ''){
            $limit = 10;
        }

        $this->model = $this->model->where([['id_viagem','=', $idViagem]])->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        return $this->model;

    }

}
