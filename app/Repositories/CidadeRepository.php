<?php

namespace App\Repositories;

class CidadeRepository extends AbstractRepository {

    public function findPorIdEstado($idEstado)
    {
        $this->model = $this->model->where('id_estado', $idEstado)->get();
        return $this->model;
    }

}
