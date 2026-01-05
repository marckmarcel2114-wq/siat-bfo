<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCompra extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_compra';
    protected $fillable = [
        'ciudad_id',
        'ubicacion_id',
        'descripcion',
        'justificacion',
        'estado_solicitud_id',
        'pdf_formulario_path',
        'fecha_solicitud',
        'fecha_aprobacion',
        'solicitado_por_id',
        'aprobado_por_id',
        'comprado_por_id',
        'proveedor_propuesto_id'
    ];
    
    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    public function ciudad() { return $this->belongsTo(City::class); }
    public function ubicacion() { return $this->belongsTo(Ubicacion::class); }
    public function estado() { return $this->belongsTo(EstadoSolicitud::class, 'estado_solicitud_id'); }
    public function solicitadoPor() { return $this->belongsTo(User::class, 'solicitado_por_id'); }
    public function aprobadoPor() { return $this->belongsTo(User::class, 'aprobado_por_id'); }
    public function compradoPor() { return $this->belongsTo(User::class, 'comprado_por_id'); }
    public function proveedorPropuesto() { return $this->belongsTo(Proveedor::class, 'proveedor_propuesto_id'); }
}
