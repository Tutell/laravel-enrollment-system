<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'enrollment_id' => 'required|exists:enrollment,enrollment_ID',
            'type' => 'required|string|max:50',
            'score' => 'required|integer|min:0|max:100',
            'weight' => 'nullable|numeric|min:0',
            'date_recorded' => 'nullable|date',
        ];
    }
}
