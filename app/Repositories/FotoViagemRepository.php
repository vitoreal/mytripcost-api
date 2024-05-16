<?php

namespace App\Repositories;

use App\Models\FotoViagem;

class FotoViagemRepository extends AbstractRepository {

    public function buscarPorIdViagem($idViagem){
        $this->model = $this->model->where('id_viagem', $idViagem)->get();
        return $this->model;
    }

    public function listarPaginationFotoViagem($idViagem, $startRow, $limit, $sortBy, $orderBy){

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
        $lista = $query->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        $result = [
            'total' => $total,
            'lista' => $lista
        ];

       return $result;

    }

}
