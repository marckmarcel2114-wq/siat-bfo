<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depreciacion extends Model
{
    use HasFactory;

    protected $table = 'depreciacion';
    protected $fillable = [
        'activo_id',
        'periodo',
        'valor_inicial',
        'depreciacion_mensual',
        'valor_neto'
    ];
    
    protected $casts = [
        'periodo' => 'date',
        'valor_inicial' => 'decimal:2',
        'depreciacion_mensual' => 'decimal:2',
        'valor_neto' => 'decimal:2',
    ];
}
