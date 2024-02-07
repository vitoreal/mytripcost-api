<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportarBug extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reportar_bugs';

    protected $fillable = [
        'titulo',
        'descricao',
        'foto',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
