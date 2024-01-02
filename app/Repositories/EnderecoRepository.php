<?php

namespace App\Repositories;

use App\Models\Endereco;

class EnderecoRepository extends AbstractRepository {

    
    public function __construct(Endereco $endereco){
        $this->endereco = $endereco;
    }

    public function buscarPorIdUsuario($id){
        $this->endereco = $this->endereco->where('user_id', $id)->first();
        return $this->endereco;
    }

}