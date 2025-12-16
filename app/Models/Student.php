<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'student_ID';

    protected $fillable = [
        'account_ID',
        'section_ID',
        'account_id',
        'section_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'gender',
        'birthdate',
        'status',
        'lrn',
        'archived_at',
        'archive_reason',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'archived_at' => 'datetime',
    ];

    public function guardians()
    {
        return $this->hasMany(Guardian::class, 'student_ID', 'student_ID');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_ID', 'account_ID');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_ID', 'section_ID');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'student_ID');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollment', 'student_id', 'course_id')
            ->using(Enrollment::class)
            ->withTimestamps();
    }

    /**
     * Grades via enrollments (hasManyThrough relationship).
     */
    public function grades()
    {
        return $this->hasManyThrough(
            \App\Models\Grade::class,
            \App\Models\Enrollment::class,
            'student_ID', // Foreign key on Enrollment table...
            'enrollment_ID', // Foreign key on Grade table...
            'student_ID', // Local key on Student table...
            'enrollment_ID' // Local key on Enrollment table (primary key)
        );
    }

    /**
     * Use `student_ID` for route model binding and URL generation (matches DB column).
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'student_ID';
    }

    /**
     * Normalize attribute access so both `student_id` and `student_ID` work.
     */
    public function getStudentIdAttribute()
    {
        return $this->attributes['student_ID'] ?? null;
    }

    public function setStudentIdAttribute($value)
    {
        $this->attributes['student_ID'] = $value;
    }

    /**
     * Normalize account_id / account_ID accessors.
     */
    public function getAccountIdAttribute()
    {
        return $this->attributes['account_ID'] ?? null;
    }

    public function setAccountIdAttribute($value)
    {
        $this->attributes['account_ID'] = $value;
    }

    /**
     * Normalize section_id / section_ID accessors.
     */
    public function getSectionIdAttribute()
    {
        return $this->attributes['section_ID'] ?? null;
    }

    public function setSectionIdAttribute($value)
    {
        $this->attributes['section_ID'] = $value;
    }
}
