<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoActivo extends Model
{
    use HasFactory;

    protected $table = 'estados_activo';
    protected $fillable = ['nombre'];
}
