<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentAudit extends Model
{
    use HasFactory;

    protected $table = 'enrollment_audits';

    protected $primaryKey = 'audit_ID';

    protected $fillable = [
        'enrollment_ID',
        'processed_by_account_ID',
        'action',
        'changes',
    ];
}
