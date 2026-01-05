<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSucursal extends Model
{
    use HasFactory;

    protected $table = 'tipos_sucursal';
    protected $fillable = ['nombre', 'descripcion', 'color', 'sort_order'];

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(Ubicacion::class, 'tipo_sucursal_id');
    }
}
