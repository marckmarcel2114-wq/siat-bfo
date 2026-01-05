<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoEjecucion extends Model
{
    use HasFactory;

    protected $table = 'estados_ejecucion';
    protected $fillable = ['nombre'];
}
