<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearLevelAssignment extends Model
{
    use HasFactory;

    protected $table = 'year_level_assignments';

    protected $primaryKey = 'assignment_ID';

    protected $fillable = [
        'year_level_ID',
        'teacher_ID',
        'status',
        'requested_at',
        'approved_by_account_ID',
        'approved_at',
        'notes',
    ];

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class, 'year_level_ID', 'year_level_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_ID', 'teacher_ID');
    }
}
