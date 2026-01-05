<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefinicionAtributo extends Model
{
    use HasFactory;

    protected $table = 'definiciones_atributos';
    protected $fillable = ['nombre', 'tipo_dato', 'opciones', 'orden'];

    protected $casts = [
        'opciones' => 'array',
    ];

    public function tiposActivo()
    {
        return $this->belongsToMany(TipoActivo::class, 'definicion_atributo_tipo_activo', 'definicion_atributo_id', 'tipo_activo_id')
                    ->withTimestamps();
    }

    public function options()
    {
        return $this->hasMany(OpcionAtributo::class, 'definicion_atributo_id');
    }
}
