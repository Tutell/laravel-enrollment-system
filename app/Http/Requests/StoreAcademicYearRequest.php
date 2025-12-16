<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'school_year' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'semester' => 'required|string|in:1st Semester,2nd Semester,Summer',
            'is_active' => 'required|boolean',
        ];
    }
}
