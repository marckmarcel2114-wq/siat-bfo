<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'asset_type_id',
        'brand',
        'model',
        'serial_number',
        'code_internal',
        'purchase_date',
        'warranty_expiry_date',
        'location_id',
        'atm_id',
        'network_point',
        'ip_address',
        'mac_address',
        'status',
        'specs',
        'notes',
    ];

    protected $casts = [
        'specs' => 'array',
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'location_id');
    }

    public function atm(): BelongsTo
    {
        return $this->belongsTo(Atm::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class)->whereNull('returned_at')->latestOfMany();
    }
    
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
    
    public function softwareLogs(): HasMany
    {
        return $this->hasMany(SoftwareLog::class);
    }
}
