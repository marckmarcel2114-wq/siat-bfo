<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'action',
        'software_name',
        'version',
        'performed_at',
        'performed_by',
        'notes'
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
