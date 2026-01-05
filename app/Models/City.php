<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $table = 'ciudades';
    protected $fillable = ['nombre', 'codigo'];

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(Ubicacion::class, 'ciudad_id');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Ubicacion::class, 'ciudad_id')->whereHas('tipo', function($q) {
            $q->where('nombre', '!=', 'ATM');
        });
    }

    public function atms(): HasMany
    {
        return $this->hasMany(Ubicacion::class, 'ciudad_id')->whereHas('tipo', function($q) {
            $q->where('nombre', 'ATM');
        });
    }
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function assets()
    {
        return $this->hasManyThrough(Activo::class, Ubicacion::class, 'ciudad_id', 'ubicacion_id');
    }
}
