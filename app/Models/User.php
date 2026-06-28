<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'username',
        'password',
        'access_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'performed_by');
    }
}
