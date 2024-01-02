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

        //$this->usuario = $this->usuario->where('name','LIKE',"%{$filter}%")->get();
        //$startRow, $limit, $sortBy
        if($sortBy == ''){
            $sortBy = 'asc';
        }
        if($startRow == ''){
            $startRow = 1;
        }
        if($limit == ''){
            $limit = 10;
        }

        $resultado = DB::table('users as u')
                        ->join('status as s', 'u.status_id', '=', 's.id')
                        ->where('u.id', '!=', 1)
                        ->where('u.id', '!=', 2)
                        ->offset($startRow)->limit($limit)->orderBy('u.'.$orderBy, $sortBy)->get(['u.id', 'u.name', 'u.email', 'u.telefone', 's.nome']);
        
        return $resultado;

    }

    public function listarTotalPagination(){

        $total = $this->model->all()
                        ->where('id', '!=', 1)
                        ->where('id', '!=', 2)
                        ->count();
    
        return  $total;

    }

}