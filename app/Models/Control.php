<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    use HasFactory;

    protected $table = 'controles';
    protected $fillable = ['tipo_activo_id', 'tipo_control_id', 'nombre', 'obligatorio'];

    public function tipoActivo()
    {
        return $this->belongsTo(TipoActivo::class);
    }
    
    public function tipoControl()
    {
        return $this->belongsTo(TipoControl::class); // We need to create TipoControl model if distinct from migration definition... Migration created 'tipos_control'. So Model 'TipoControl'.
    }
}
