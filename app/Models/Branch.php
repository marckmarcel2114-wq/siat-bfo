<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'city_id',
        'branch_type_id',
        'parent_id',
        'code',
        'name',
        'address',
        'phones',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    public function type()
    {
        return $this->belongsTo(BranchType::class, 'branch_type_id');
    }

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }
}
