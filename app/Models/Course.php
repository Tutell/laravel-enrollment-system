<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $primaryKey = 'course_ID';

    protected $fillable = [
        'subject_ID',
        'teacher_ID',
        'academic_year_ID',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'course_code',
        'schedule',
        'room_number',
        'max_capacity',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_ID', 'subject_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_ID', 'teacher_ID');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_ID', 'academic_year_ID');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_ID', 'course_ID');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollment', 'course_ID', 'student_ID')
            ->using(Enrollment::class)
            ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'course_ID';
    }

    public function getCourseIdAttribute()
    {
        return $this->attributes['course_ID'] ?? null;
    }

    public function setSubjectIdAttribute($value)
    {
        $this->attributes['subject_ID'] = $value;
    }

    public function setTeacherIdAttribute($value)
    {
        $this->attributes['teacher_ID'] = $value;
    }

    public function setAcademicYearIdAttribute($value)
    {
        $this->attributes['academic_year_ID'] = $value;
    }

    public function setCourseIdAttribute($value)
    {
        $this->attributes['course_ID'] = $value;
    }
}
