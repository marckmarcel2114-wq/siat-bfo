<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios';
    protected $fillable = [
        'activo_id',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'fecha',
        'usuario_responsable_id'
    ];
    
    protected $casts = ['fecha' => 'datetime'];

    public function activo() { return $this->belongsTo(Activo::class); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_responsable_id'); }
}
