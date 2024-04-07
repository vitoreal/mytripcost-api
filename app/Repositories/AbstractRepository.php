<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {

    public function __construct(Model $model){
        $this->model = $model;
    }

    public function findAll()
    {
        $this->model = $this->model->all();
        return  $this->model;
    }

    public function salvar($request){

        $resposta = $request->save();
        return $resposta; // true or false

    }

    public function buscarPorId($id){
        $this->model = $this->model->find($id);
        return $this->model;
    }

    public function totalRegistroPorIdUser($id){
        $total = $this->model->where('user_id', $id)->count();
        return $total;
    }

    public function buscarRegistroPorIdUser($id){
        $this->model = $this->model->where('user_id', $id)->get();
        return $this->model;
    }

    public function excluir($id){
        $model = $this->model->find($id);

        $model->delete();

        //$deleted = $this->model->where('id', $id)->delete();
    }


    public function listarTotalPagination(){

        $total = $this->model->all()->count();

        return  $total;

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

        $this->model = $this->model->offset($startRow)->limit($limit)->orderBy($orderBy, $sortBy)->get();

        return $this->model;

    }


}
