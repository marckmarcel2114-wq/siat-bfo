<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasFactory;

    protected $table = 'software';

    protected $fillable = [
        'nombre',
        'fabricante',
        'tipo',
        'descripcion'
    ];

    public function versions()
    {
        return $this->hasMany(SoftwareVersion::class)->orderBy('version', 'desc');
    }
}
