<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,student_ID',
            'course_id' => 'required|exists:courses,course_ID',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|string|in:Enrolled,Dropped,Completed,Pending',
        ];
    }
}
