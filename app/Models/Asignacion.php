<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asignaciones';
    protected $fillable = [
        'activo_id',
        'usuario_id',
        'fecha_asignacion',
        'fecha_devolucion',
        'acta_entrega_path',
        'acta_devolucion_path',
        'es_actual'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_devolucion' => 'datetime',
        'es_actual' => 'boolean'
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
