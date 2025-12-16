<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $table = 'guardians';

    protected $primaryKey = 'guardian_ID';

    protected $fillable = [
        'student_ID',
        'full_name',
        'relationship',
        'contact_number',
        'email',
        'address',
        'occupation',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_ID', 'student_ID');
    }
}
