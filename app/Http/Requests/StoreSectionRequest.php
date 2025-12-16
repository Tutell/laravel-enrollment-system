<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'teacher_id' => 'required|exists:teachers,teacher_id',
            'section_name' => 'required|string|max:100',
            'grade_level' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
        ];
    }
}
