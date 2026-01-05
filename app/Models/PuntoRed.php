<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoRed extends Model
{
    use HasFactory;

    protected $table = 'puntos_red';
    protected $fillable = ['ubicacion_id', 'patch_panel', 'roseta', 'switch', 'descripcion'];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }
}
