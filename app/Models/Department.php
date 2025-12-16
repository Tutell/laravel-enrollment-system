<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $primaryKey = 'department_ID';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'department_ID', 'department_ID');
    }
}
