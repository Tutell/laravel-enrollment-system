<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Enrollment extends Pivot
{
    use HasFactory;

    protected $table = 'enrollment';

    protected $primaryKey = 'enrollment_ID';

    public $incrementing = true;

    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_date',
        'status',
        'processed_by_account_ID',
        'processed_at',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_ID');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_ID');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'enrollment_ID', 'enrollment_ID');
    }

    public function getStudentIdAttribute()
    {
        return $this->attributes['student_ID'] ?? $this->attributes['student_id'] ?? null;
    }

    public function setStudentIdAttribute($value)
    {
        $this->attributes['student_ID'] = $value;
    }

    public function getCourseIdAttribute()
    {
        return $this->attributes['course_ID'] ?? $this->attributes['course_id'] ?? null;
    }

    public function setCourseIdAttribute($value)
    {
        $this->attributes['course_ID'] = $value;
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
