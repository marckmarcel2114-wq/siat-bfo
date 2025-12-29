<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'city_id',
        'status',
        'items',
        'justification',
        'authorization_document_path',
        'authorized_at',
        'authorized_by'
    ];

    protected $casts = [
        'items' => 'array',
        'authorized_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
