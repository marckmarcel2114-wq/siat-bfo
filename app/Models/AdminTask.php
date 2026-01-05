<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminTask extends Model
{
    use HasFactory;

    protected $table = 'tareas_admin';

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'assigned_city_id',
        'assigned_user_id',
        'task_type_id',
        'due_date',
        'completed_at',
        'completed_by',
        'proof_document_path',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function type(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function assignedCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'assigned_city_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
