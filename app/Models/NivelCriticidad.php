<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelCriticidad extends Model
{
    use HasFactory;

    protected $table = 'niveles_criticidad';
    protected $fillable = ['nombre', 'nivel_numerico', 'color'];
}
