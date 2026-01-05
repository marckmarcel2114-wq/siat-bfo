<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionAtributo extends Model
{
    use HasFactory;

    protected $table = 'opciones_atributos';
    protected $fillable = ['definicion_atributo_id', 'nombre'];

    public function definition()
    {
        return $this->belongsTo(DefinicionAtributo::class, 'definicion_atributo_id');
    }
}
