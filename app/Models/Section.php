<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';

    protected $primaryKey = 'section_ID';

    protected $fillable = [
        'teacher_ID',
        'section_name',
        'grade_level',
        'capacity',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_ID', 'teacher_ID');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'section_teacher', 'section_ID', 'teacher_ID')
            ->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'section_ID', 'section_ID');
    }

    // Accessors for lowercase attribute access
    public function getSectionIdAttribute()
    {
        return $this->attributes['section_ID'] ?? null;
    }

    public function setSectionIdAttribute($value)
    {
        $this->attributes['section_ID'] = $value;
    }

    public function getTeacherIdAttribute()
    {
        return $this->attributes['teacher_ID'] ?? null;
    }

    public function setTeacherIdAttribute($value)
    {
        $this->attributes['teacher_ID'] = $value;
    }

    public function getRouteKeyName()
    {
        return 'section_ID';
    }
}
