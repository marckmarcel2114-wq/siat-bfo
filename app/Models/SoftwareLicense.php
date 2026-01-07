<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    use HasFactory;

    protected $table = 'software_licenses';

    protected $fillable = [
        'nombre',
        'software_id', // Link to Catalog
        'key', // clave del software
        'tipo', // OEM, Volume, Subscription, Free
        'seats_total',
        'seats_used',
        'fecha_expiracion',
        'proveedor_id',
        'scope',
        'city_id',
        'observaciones'
    ];

    protected $casts = [
        'fecha_expiracion' => 'date',
    ];

    public function software()
    {
        return $this->belongsTo(Software::class, 'software_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function instalaciones()
    {
        return $this->hasMany(SoftwareInstallation::class, 'license_id');
    }
}
