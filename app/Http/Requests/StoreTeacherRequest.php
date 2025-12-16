<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_id' => 'required|exists:accounts,account_ID',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'contact_number' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,department_ID',
        ];
    }
}
