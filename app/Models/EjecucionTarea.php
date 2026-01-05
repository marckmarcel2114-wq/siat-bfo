<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EjecucionTarea extends Model
{
    use HasFactory;

    protected $table = 'ejecuciones_tarea';
    protected $fillable = [
        'tarea_id',
        'admin_ciudad_id',
        'ubicacion_id',
        'fecha_ejecucion',
        'observaciones',
        'acta_ejecucion_path',
        'estado_ejecucion_id'
    ];
    
    protected $casts = [
        'fecha_ejecucion' => 'date',
    ];

    public function tarea() { return $this->belongsTo(TareaSupervisor::class); }
    public function adminCiudad() { return $this->belongsTo(User::class, 'admin_ciudad_id'); }
    public function ubicacion() { return $this->belongsTo(Ubicacion::class); }
    public function estado() { return $this->belongsTo(EstadoEjecucion::class, 'estado_ejecucion_id'); }
}
