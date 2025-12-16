<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $primaryKey = 'teacher_ID';

    protected $fillable = [
        'account_ID',
        'account_id',
        'department_ID',
        'department_id',
        'first_name',
        'last_name',
        'contact_number',
        'department',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_ID', 'account_ID');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_teacher', 'teacher_ID', 'section_ID')
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_ID', 'teacher_ID');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_ID', 'subject_ID')
            ->withTimestamps();
    }

    public function qualifiedSubjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject_qualifications', 'teacher_ID', 'subject_ID')
            ->withTimestamps();
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_ID', 'department_ID');
    }

    public function getTeacherIdAttribute()
    {
        return $this->attributes['teacher_ID'] ?? $this->attributes['teacher_id'] ?? null;
    }

    public function setTeacherIdAttribute($value)
    {
        $this->attributes['teacher_ID'] = $value;
        $this->attributes['teacher_id'] = $value;
    }

    public function getAccountIdAttribute()
    {
        return $this->attributes['account_ID'] ?? $this->attributes['account_id'] ?? null;
    }

    public function setAccountIdAttribute($value)
    {
        $this->attributes['account_ID'] = $value;
    }

    public function getDepartmentIdAttribute()
    {
        return $this->attributes['department_ID'] ?? $this->attributes['department_id'] ?? null;
    }

    public function setDepartmentIdAttribute($value)
    {
        $this->attributes['department_ID'] = $value;
    }

    public function getRouteKeyName()
    {
        return 'teacher_ID';
    }
}
