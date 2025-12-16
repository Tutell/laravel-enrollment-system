<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeAudit extends Model
{
    use HasFactory;

    protected $table = 'grade_audits';

    protected $primaryKey = 'audit_ID';

    protected $fillable = [
        'grade_id',
        'enrollment_id',
        'actor_account_ID',
        'action',
        'changes',
    ];
}

