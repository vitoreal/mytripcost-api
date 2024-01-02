<?php

namespace App\Repositories;

class StatusRepository extends AbstractRepository {

    
    public function verificarNomeExiste($nome){
        $total= $this->model->where('nome', $nome)->count();
        return $total;
    }

}