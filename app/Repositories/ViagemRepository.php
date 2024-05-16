<?php

namespace App\Repositories;

class ViagemRepository extends AbstractRepository {

    public function buscarViagemPorId($id){
        $this->model = $this->model->where([['status', '=', '1'], ['id', '=', $id]])->first();
        return $this->model;
    }

    public function buscarViagemPorUser($idUser, $idViagem){
        $this->model = $this->model->where([['user_id', '=', $idUser], ['id', '=', $idViagem]])->first();
        return $this->model;
    }

    public function verificarNomeExiste($nome, $idUser, $idViagem){
        $total= $this->model->where([['nome', '=', $nome], ['user_id', '=', $idUser], ['id', '!=', $idViagem]])->count();
        return $total;
    }

    public function listarPaginationViagem($startRow, $limit, $sortBy, $orderBy, $userId){

        if($sortBy == ''){
            $sortBy = 'asc';
        }
        if($startRow == ''){
            $startRow = 1;
        }
        if($limit == ''){
            $limit = 10;
        }


        $query = $this->model->where([['user_id','=', $userId], ['status', '=', 1]]);

        $total = $query->count();
        $lista = $query->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        $result = [
            'total' => $total,
            'lista' => $lista
        ];

       return $result;

    }

}
