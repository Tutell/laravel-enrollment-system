<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $primaryKey = 'subject_ID';

    protected $fillable = ['name', 'description', 'grade_level'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'subject_ID', 'subject_ID');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher', 'subject_ID', 'teacher_ID')
            ->withTimestamps();
    }

    public function getSubjectIdAttribute()
    {
        return $this->attributes['subject_ID'] ?? null;
    }

    public function setSubjectIdAttribute($value)
    {
        $this->attributes['subject_ID'] = $value;
    }

    public function getRouteKeyName()
    {
        return 'subject_ID';
    }
}
