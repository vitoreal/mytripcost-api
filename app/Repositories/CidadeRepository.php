<?php

namespace App\Repositories;

use App\Models\Cidade;

class CidadeRepository extends AbstractRepository {

    public function __construct(Cidade $cidade){
        $this->cidade = $cidade;
    }

    public function findPorIdEstado($idEstado)
    {
        $this->cidade = $this->cidade::where('id_estado', $idEstado)->get();
        return $this->cidade;
    }

}