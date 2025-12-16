<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $primaryKey = 'academic_year_ID';

    protected $fillable = [
        'school_year',
        'semester',
        'is_active',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'academic_year_ID', 'academic_year_ID');
    }

    public function getRouteKeyName()
    {
        return 'academic_year_ID';
    }

    public function getAcademicYearIdAttribute()
    {
        return $this->attributes['academic_year_ID'] ?? null;
    }

    public function setAcademicYearIdAttribute($value)
    {
        $this->attributes['academic_year_ID'] = $value;
    }
}
