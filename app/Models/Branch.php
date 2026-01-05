<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;
    
    protected $table = 'ubicaciones';

    protected $fillable = [
        'ciudad_id',
        'tipo_sucursal_id',
        'padre_id',
        'nombre',
        'direccion',
        'telefonos',
        'codigo_ubicacion'
    ];

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(City::class, 'ciudad_id');
    }
    
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoSucursal::class, 'tipo_sucursal_id');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'padre_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Activo::class, 'ubicacion_id');
    }
}
