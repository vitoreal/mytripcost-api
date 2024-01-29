<?php

namespace App\Repositories;

class CategoriaRepository extends AbstractRepository {


    public function verificarNomeExiste($nome){
        $total= $this->model->where('nome', $nome)->count();
        return $total;
    }

}
