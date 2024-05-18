<?php

namespace App\Repositories;

class DespesaRepository extends AbstractRepository {

    public function verificarNomeExiste($nome, $idViagem, $idDespesa){
        $total= $this->model->where([['nome', '=', $nome], ['id_viagem', '=', $idViagem], ['id', '!=', $idDespesa]])->count();
        return $total;
    }

    public function buscarRegistroPorIdViagem($id){
        $result = $this->model->where('id_viagem', $id)->get();
        return $result;
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

        $query = $this->model->where('id_viagem', $idViagem);

        $total = $query->count();
        $lista = $query->with('categoria')->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        $result = [
            'total' => $total,
            'lista' => $lista
        ];

       return $result;

    }

}
