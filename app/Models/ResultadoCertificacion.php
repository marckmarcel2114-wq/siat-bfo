<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoCertificacion extends Model
{
    use HasFactory;

    protected $table = 'resultados_certificacion';
    protected $fillable = ['nombre'];
}
