<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cidade';

    protected $fillable = [
        'nome',
        'id_estado',
    ];

    
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

}
