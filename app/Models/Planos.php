<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planos extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planos';

    protected $fillable = [
        'nome',
        'calcular_moeda',
        'qtd_amigos',
        'qtd_foto',
        'qtd_categoria',
    ];
}
