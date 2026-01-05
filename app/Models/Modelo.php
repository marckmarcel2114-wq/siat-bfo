<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $table = 'modelos';
    protected $fillable = ['marca_id', 'tipo_activo_id', 'nombre'];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function tipoActivo()
    {
        return $this->belongsTo(TipoActivo::class);
    }
}
