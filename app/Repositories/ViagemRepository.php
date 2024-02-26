<?php

namespace App\Repositories;

use App\Models\Viagem;

class ViagemRepository extends AbstractRepository {

    public function __construct(Viagem $viagem){
        $this->viagem = $viagem;
    }

    public function verificarNomeExiste($nome, $idUser, $idViagem){
        $total= $this->viagem->where([['nome', '=', $nome], ['user_id', '=', $idUser], ['id', '!=', $idViagem]])->count();
        return $total;
    }

    public function listarTotalPaginationViagem($userId){

        $total = $this->viagem->where('user_id','=', $userId)->count();

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

        $this->viagem = $this->viagem->where('user_id','=', $userId)->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        return $this->viagem;

    }

}
