<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'maintenance_type_id',
        'description',
        'technician_name',
        'performed_at',
        'report_document_path',
        'cost'
    ];

    protected $casts = [
        'performed_at' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
    
    public function type(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }
}
