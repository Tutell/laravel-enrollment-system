<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAccessLog extends Model
{
    use HasFactory;

    protected $table = 'teacher_access_logs';

    protected $primaryKey = 'log_ID';

    protected $fillable = [
        'account_ID',
        'teacher_ID',
        'action',
        'ip_address',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_ID', 'account_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_ID', 'teacher_ID');
    }
}
