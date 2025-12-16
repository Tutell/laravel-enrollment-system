<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountAudit extends Model
{
    use HasFactory;

    protected $table = 'account_audits';

    protected $primaryKey = 'audit_ID';

    protected $fillable = [
        'actor_account_ID',
        'target_account_ID',
        'action',
        'changes',
    ];
}
