<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Doctor extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Auto bcrypt password when setting it
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}
