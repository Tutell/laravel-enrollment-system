<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $primaryKey = 'payment_ID';

    protected $fillable = [
        'account_ID', 'student_ID', 'enrollment_ID',
        'provider', 'amount', 'currency', 'status', 'reference', 'checkout_url',
    ];
}
