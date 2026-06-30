<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'room_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Satu user bisa menjadi PIC di banyak ruangan
    public function rooms()
    {
        return $this->hasMany(Room::class, 'user_id');
    }

    public function hasRole($role)
    {
        if (!$this->role) {
            return false;
        }
        
        if (is_array($role)) {
            foreach ($role as $r) {
                if (str_contains(strtolower($this->role->name), strtolower($r))) {
                    return true;
                }
            }
            return false;
        }
        
        return str_contains(strtolower($this->role->name), strtolower($role));
    }
}