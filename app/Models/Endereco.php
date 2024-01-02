<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'endereco';

    protected $fillable = [
        'cep',
        'id_cidade',
        'bairro',
        'endereco',
        'numero',
        'complemento',
        'user_id',
    ];

    /**
     * The roles that belong to the user.
     */
    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'id_cidade');
    }
}
