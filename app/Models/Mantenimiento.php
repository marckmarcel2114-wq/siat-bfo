<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';
    protected $fillable = [
        'activo_id',
        'proveedor_id',
        'tipo_mantenimiento_id',
        'estado_mantenimiento_id',
        'fecha_inicio',
        'costo_bs',
        'hoja_trabajo',
        'nota_servicio_path'
    ];
    
    protected $casts = ['fecha_inicio' => 'date', 'costo_bs' => 'decimal:2'];

    public function activo() { return $this->belongsTo(Activo::class); }
    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    // Add models for TipoMantenimiento / EstadoMantenimiento... simpler to just relate or treat as lookup
}
