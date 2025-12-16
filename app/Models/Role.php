<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'role_ID';

    protected $fillable = ['role_name'];

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_roles', 'role_ID', 'account_ID')
            ->withTimestamps();
    }
}
