<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificacionRed extends Model
{
    use HasFactory;

    protected $table = 'certificaciones_red';
    protected $fillable = [
        'punto_red_id',
        'tipo_certificacion',
        'resultado_id',
        'fecha_certificacion',
        'tecnico_responsable',
        'informe_certificacion_path',
        'observaciones'
    ];
    
    protected $casts = ['fecha_certificacion' => 'datetime'];

    public function puntoRed()
    {
        return $this->belongsTo(PuntoRed::class);
    }

    public function resultado()
    {
        return $this->belongsTo(ResultadoCertificacion::class, 'resultado_id');
    }
}
