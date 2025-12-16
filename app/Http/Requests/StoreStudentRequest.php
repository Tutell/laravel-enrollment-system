<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // If this request is used for updating, allow the current student's account_id
        $student = $this->route('student');

        $accountUniqueRule = 'unique:students,account_ID';
        if ($student) {
            // exclude current student by primary key (student_ID)
            $accountUniqueRule .= ','.$student->student_ID.',student_ID';
        }

        $rules = [
            'account_id' => ['nullable', 'exists:accounts,account_ID', $accountUniqueRule],
            'section_id' => 'required|exists:sections,section_ID',
            'grade_level' => 'required|integer|min:1|max:12',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'birthdate' => 'required|date|before:today|before_or_equal:' . now()->subYears(12)->format('Y-m-d'),
        ];

        if (! $student) {
            $rules['lrn'] = 'required|digits:12|unique:students,lrn';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'birthdate.before_or_equal' => 'Student must be at least 12 years old to enroll.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $gradeLevel = $this->input('grade_level');
            $sectionId = $this->input('section_id');

            if ($gradeLevel && $sectionId) {
                $section = \App\Models\Section::find($sectionId);
                if ($section && $section->grade_level != $gradeLevel) {
                    $validator->errors()->add('section_id', 'The selected section does not match the selected grade level.');
                }
            }
        });
    }
}
