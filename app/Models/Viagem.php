<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viagem extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'viagem';

    protected $fillable = [
        'privado',
        'descricao',
        'nome',
        'orcamento',
        'data_inicio',
        'data_fim',
        'foto',
        'id_moeda',
    ];
}
