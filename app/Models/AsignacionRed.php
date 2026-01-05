<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionRed extends Model
{
    use HasFactory;

    protected $table = 'asignaciones_red';
    protected $fillable = [
        'activo_id',
        'punto_red_id',
        'ip_address',
        'mac_ethernet',
        'mac_wifi',
        'numero_interno',
        'fecha_asignacion',
        'fecha_baja',
        'es_actual'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_baja' => 'datetime',
        'es_actual' => 'boolean'
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
    
    public function puntoRed()
    {
        return $this->belongsTo(PuntoRed::class);
    }
}
