<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paises extends Model
{
    use HasFactory;

    use HasFactory;

    public $timestamps = false;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises';

    protected $fillable = [
        'nome',
        'iso',
        'iso3',
        'dial',
        'currency',
        'currency_name',
    ];

}
