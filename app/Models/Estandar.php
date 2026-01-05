<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estandar extends Model
{
    use HasFactory;

    protected $table = 'estandares';
    protected $fillable = [
        'tipo_estandar_id',
        'tipo_activo_id',
        'atributo_clave',
        'operador_id',
        'valor_esperado',
        'descripcion'
    ];
}
