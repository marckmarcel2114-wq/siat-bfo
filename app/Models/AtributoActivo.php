<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoActivo extends Model
{
    use HasFactory;

    protected $table = 'atributos_activos';
    protected $fillable = ['activo_id', 'definicion_atributo_id', 'valor'];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }

    public function definicion()
    {
        return $this->belongsTo(DefinicionAtributo::class, 'definicion_atributo_id');
    }

    // Accessor legacy support (optional, but good for views if they access ->nombre)
    // Actually, 'nombre' column is gone. We need to get it from relation.
    public function getNombreAttribute()
    {
        return $this->definicion->nombre ?? 'Desconocido';
    }
}
