<?php

namespace App\Repositories;

class DespesaRepository extends AbstractRepository {

    public function verificarNomeExiste($nome, $idViagem, $idDespesa){
        $total= $this->model->where([['nome', '=', $nome], ['id_viagem', '=', $idViagem], ['id', '!=', $idDespesa]])->count();
        return $total;
    }


}
