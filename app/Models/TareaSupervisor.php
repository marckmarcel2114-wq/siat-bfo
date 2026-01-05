<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaSupervisor extends Model
{
    use HasFactory;

    protected $table = 'tareas_supervisor';
    protected $fillable = [
        'supervisor_id',
        'titulo',
        'descripcion',
        'fecha_asignacion',
        'fecha_limite',
        'estado_tarea_id'
    ];
    
    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_limite' => 'date',
    ];

    public function supervisor() { return $this->belongsTo(User::class, 'supervisor_id'); }
    public function estado() { return $this->belongsTo(EstadoTarea::class, 'estado_tarea_id'); }
    public function ejecuciones() { return $this->hasMany(EjecucionTarea::class, 'tarea_id'); }
}
