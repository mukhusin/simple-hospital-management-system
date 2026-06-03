<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isDoctorOffice()
    {
        if ($this->office_id == 1 || $this->office_id == 2) {
            return true;
        }
        return false;
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
