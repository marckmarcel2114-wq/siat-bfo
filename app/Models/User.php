<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'city_id',
        'position',
        'ad_guid',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
    
    // Relationships
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
    
    public function currentAssets()
    {
        return $this->assetAssignments()->whereNull('returned_at')->with('asset');
    }
    
    public function createdTasks()
    {
        return $this->hasMany(AdminTask::class, 'created_by');
    }
    
    public function assignedTasks()
    {
        return $this->hasMany(AdminTask::class, 'assigned_user_id');
    }
}
