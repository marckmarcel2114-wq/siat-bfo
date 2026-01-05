<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoActivo extends Model
{
    use HasFactory;

    protected $table = 'tipos_activo';
    protected $fillable = ['nombre'];

    public function assets()
    {
        return $this->hasMany(Activo::class);
    }

    public function definitions()
    {
        return $this->belongsToMany(DefinicionAtributo::class, 'definicion_atributo_tipo_activo', 'tipo_activo_id', 'definicion_atributo_id')
                    ->withTimestamps()
                    ->orderBy('orden');
    }
}
