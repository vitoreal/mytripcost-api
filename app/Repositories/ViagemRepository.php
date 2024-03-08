<?php

namespace App\Repositories;

use App\Models\Viagem;

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

    public function listarTotalPaginationViagem($userId){

        $total = $this->model->where([['user_id','=', $userId], ['status', '=', 1]])->count();

        return  $total;

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

        $this->model = $this->model->where([['user_id','=', $userId], ['status', '=', 1]])->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        return $this->model;

    }

}
