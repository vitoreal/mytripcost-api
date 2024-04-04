<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoViagem extends Model
{
    use HasFactory;

    public $timestamps = false;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fotos_viagem';

    protected $fillable = ['foto', 'id_viagem', 'mimetype', 'extension'];

}
