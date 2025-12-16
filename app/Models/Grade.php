<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';

    protected $primaryKey = 'grade_ID';

    protected $fillable = [
        'enrollment_id',
        'type',
        'score',
        'weight',
        'date_recorded',
    ];

    protected $casts = [
        'date_recorded' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_ID', 'enrollment_ID');
    }

    public function getGradeIdAttribute()
    {
        return $this->attributes['grade_ID'] ?? $this->attributes['grade_id'] ?? null;
    }

    public function setGradeIdAttribute($value)
    {
        $this->attributes['grade_ID'] = $value;
    }

    public function getEnrollmentIdAttribute()
    {
        return $this->attributes['enrollment_ID'] ?? $this->attributes['enrollment_id'] ?? null;
    }

    public function setEnrollmentIdAttribute($value)
    {
        $this->attributes['enrollment_ID'] = $value;
    }
}
