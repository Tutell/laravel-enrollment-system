<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // if using auth
use Illuminate\Notifications\Notifiable;

class Account extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'accounts';

    protected $primaryKey = 'account_ID';  // Match database column name (uppercase)

    public $incrementing = true;

    protected $fillable = [
        'Username',
        'Password_Hash',
        'Email',
        'role',
        'status',
    ];

    protected $hidden = [
        'Password_Hash',
    ];

    // Accessors for lowercase attribute access
    public function getAccountIdAttribute()
    {
        return $this->attributes['account_ID'] ?? null;
    }

    public function setAccountIdAttribute($value)
    {
        $this->attributes['account_ID'] = $value;
    }

    public function getUsernameAttribute()
    {
        return $this->attributes['Username'] ?? null;
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['Username'] = $value;
    }

    public function getPasswordHashAttribute()
    {
        return $this->attributes['Password_Hash'] ?? null;
    }

    public function setPasswordHashAttribute($value)
    {
        $this->attributes['Password_Hash'] = $value;
    }

    public function getEmailAttribute()
    {
        return $this->attributes['Email'] ?? null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['Email'] = $value;
    }

    // If you want Laravel's auth to use password, map attribute
    public function getAuthPassword()
    {
        return $this->attributes['Password_Hash'] ?? null;
    }

    // Relationships
    public function student()
    {
        return $this->hasOne(Student::class, 'account_ID', 'account_ID');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'account_ID', 'account_ID');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'account_roles', 'account_ID', 'role_ID')
            ->withTimestamps();
    }
}
