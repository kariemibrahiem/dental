<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Patient extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'doctor_id',
        'result',
        'date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'date' => 'date',
    ];

    // Auto bcrypt password when setting it
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
