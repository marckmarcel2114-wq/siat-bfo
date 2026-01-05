<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareInstallation extends Model
{
    use HasFactory;

    protected $table = 'software_installations';

    protected $fillable = [
        'license_id',
        'software_version_id',
        'activo_id',
        'fecha_instalacion',
        'registrado_por', // User ID
        'observaciones'
    ];

    protected $casts = [
        'fecha_instalacion' => 'date',
    ];

    public function license()
    {
        return $this->belongsTo(SoftwareLicense::class, 'license_id');
    }

    public function asset()
    {
        return $this->belongsTo(Activo::class, 'activo_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function softwareVersion()
    {
        return $this->belongsTo(SoftwareVersion::class, 'software_version_id');
    }
}
