<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsuarioRepository extends AbstractRepository {


    public function alterarSenha($password, $email){

        $this->model = $this->model->where('email', $email)
        ->update(['password' => $password]);
        return $this->model;
    }

    public function listarPagination($startRow, $limit, $sortBy, $orderBy){

        if($sortBy == ''){
            $sortBy = 'asc';
        }
        if($startRow == ''){
            $startRow = 1;
        }
        if($limit == ''){
            $limit = 10;
        }

        $query = DB::table('users as u')
                ->join('status as s', 'u.status_id', '=', 's.id')
                ->where('u.id', '!=', 1)
                ->where('u.id', '!=', 2);

        $total = $query->count();
        $lista = $query->offset($startRow)->limit($limit)->orderBy('u.'.$orderBy, $sortBy)->get(['u.id', 'u.name', 'u.email', 'u.telefone', 's.nome']);

        $result = [
            'total' => $total,
            'lista' => $lista
        ];

       return $result;

    }

}
