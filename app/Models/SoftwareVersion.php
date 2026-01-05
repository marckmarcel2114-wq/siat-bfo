<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareVersion extends Model
{
    use HasFactory;

    protected $table = 'software_versions';

    protected $fillable = [
        'software_id',
        'version',
        'fecha_lanzamiento',
        'eol_date',
        'descripcion'
    ];

    protected $casts = [
        'fecha_lanzamiento' => 'date',
        'eol_date' => 'date'
    ];

    public function software()
    {
        return $this->belongsTo(Software::class);
    }
}
