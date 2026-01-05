<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cumplimiento extends Model
{
    use HasFactory;

    protected $table = 'cumplimientos';
    protected $fillable = [
        'activo_id',
        'control_id',
        'estado_id',
        'fecha_verificacion',
        'usuario_responsable',
        'evidencia_path'
    ];

    protected $casts = [
        'fecha_verificacion' => 'datetime',
    ];

    public function activo() { return $this->belongsTo(Activo::class); }
    public function control() { return $this->belongsTo(Control::class); }
    public function estado() { return $this->belongsTo(EstadoCumplimiento::class, 'estado_id'); } // Create EstadoCumplimiento
}
