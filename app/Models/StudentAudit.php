<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAudit extends Model
{
    use HasFactory;

    protected $table = 'student_audits';

    protected $primaryKey = 'audit_ID';

    protected $fillable = [
        'student_ID',
        'actor_account_ID',
        'action',
        'reason',
        'changes',
    ];
}

