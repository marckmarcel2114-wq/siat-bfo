<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atm extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'parent_id',
        'name',
        'address',
        'phones',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
