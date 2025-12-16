<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'subject_id' => 'required|exists:subjects,subject_ID',
            'teacher_id' => 'required|exists:teachers,teacher_ID',
            'academic_year_id' => 'required|exists:academic_years,academic_year_ID',
            'course_code' => 'required|string|max:50',
            'schedule' => 'required|string|max:100',
            'room_number' => 'required|string|max:50',
            'max_capacity' => 'required|integer|min:1',
        ];
    }
}
