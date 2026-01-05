<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atm extends Model
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

    public function ciudad()
    {
        return $this->belongsTo(City::class, 'ciudad_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoSucursal::class, 'tipo_sucursal_id');
    }

    public function padre()
    {
        return $this->belongsTo(Ubicacion::class, 'padre_id');
    }

    public function assets()
    {
        return $this->hasMany(Activo::class, 'ubicacion_id');
    }
}
