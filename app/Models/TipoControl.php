<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoControl extends Model
{
    use HasFactory;

    protected $table = 'tipos_control';
    protected $fillable = ['nombre'];
}
