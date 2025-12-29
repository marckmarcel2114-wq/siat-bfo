<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchType extends Model
{
    protected $fillable = ['name', 'description', 'color', 'sort_order'];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
