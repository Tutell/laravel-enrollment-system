<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandingAudit extends Model
{
    use HasFactory;

    protected $table = 'branding_audits';

    protected $primaryKey = 'audit_ID';

    protected $fillable = [
        'branding_ID',
        'actor_account_ID',
        'action',
        'changes',
    ];
}

