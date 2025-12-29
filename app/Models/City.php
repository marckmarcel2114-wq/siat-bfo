<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['name', 'code'];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function atms(): HasMany
    {
        return $this->hasMany(Atm::class);
    }
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function assets()
    {
        // This is tricky now because assets can be in branches OR atms.
        // For now, I'll return assets directly associated with branches.
        // If needed, we can create a more complex relation.
        return $this->hasManyThrough(Asset::class, Branch::class, 'city_id', 'location_id');
    }
}
