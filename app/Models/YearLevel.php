<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearLevel extends Model
{
    use HasFactory;

    protected $table = 'year_levels';

    protected $primaryKey = 'year_level_ID';

    protected $fillable = ['grade_level', 'student_count', 'status'];

    public function assignments()
    {
        return $this->hasMany(YearLevelAssignment::class, 'year_level_ID', 'year_level_ID');
    }
}
