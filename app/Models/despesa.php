<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class despesa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'despesa';

    protected $fillable = [
        'nome',
        'valor',
        'data_despesa',
        'id_moeda',
        'id_viagem',
        'id_categoria',
        'id_categoria_personalizada',
        'id_metodo_pagamento',
        'outros_metodo_pagamento'
    ];

}
