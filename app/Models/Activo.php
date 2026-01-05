<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory;

    protected $table = 'activos';
    
    protected $fillable = [
        'tipo_activo_id',
        'modelo_id',
        'estado_activo_id',
        'criticidad_id',
        'propietario_id',
        'ubicacion_id',
        'detalle_ubicacion', // Specific location details (floor, office)
        'codigo_activo',
        'numero_serie',
        'mac_ethernet',
        'mac_wifi',
        'fecha_adquisicion',
        'valor_adquisicion',
        'valor_residual',
        'vida_util_anios',
        'ruta_ficha_tecnica',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'valor_adquisicion' => 'decimal:2',
        'valor_residual' => 'decimal:2',
    ];

    // Relaciones catálogo
    public function tipoActivo() { return $this->belongsTo(TipoActivo::class); }
    public function modelo() { return $this->belongsTo(Modelo::class); }
    public function estadoActivo() { return $this->belongsTo(EstadoActivo::class); }
    public function nivelCriticidad() { return $this->belongsTo(NivelCriticidad::class, 'criticidad_id'); }
    public function propietario() { return $this->belongsTo(Propietario::class); }
    public function ubicacion() { return $this->belongsTo(Ubicacion::class); }

    // Relación EAV
    public function atributos()
    {
        return $this->hasMany(AtributoActivo::class);
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class)->orderByDesc('fecha_inicio');
    }

    public function softwareInstallations()
    {
        return $this->hasMany(SoftwareInstallation::class, 'activo_id');
    }

    public function assignments()
    {
        return $this->hasMany(Asignacion::class, 'activo_id');
    }

    public function networkAssignment()
    {
        // Assuming we want the current one
        return $this->hasOne(AsignacionRed::class, 'activo_id')->where('es_actual', true);
    }
    
    public function softwareLogs()
    {
        return $this->hasMany(SoftwareLog::class, 'asset_id')->orderByDesc('performed_at');
    }
    
    // Auxiliar para obtener valor de atributo fácilmente
    public function getAtributo($nombre) {
        $attr = $this->atributos->where('nombre', $nombre)->first();
        return $attr ? $attr->valor : null;
    }
}
