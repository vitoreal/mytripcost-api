<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Despesa extends Model
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
        'id_metodo_pagamento',
        'outros_metodo_pagamento'
    ];

    /**
     * The roles that belong to the user.
     */
    public function viagem(): HasOne
    {
        return $this->hasOne(Viagem::class, 'id', 'id_viagem');
    }

}
