<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Get the user that owns the phone.
     */
    public function viagem()
    {
        return $this->belongsTo(Viagem::class, 'id_viagem', 'id');
    }


    /**
     * Get the categoria associated with the despesa.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id');
    }
}
