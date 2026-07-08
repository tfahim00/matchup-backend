<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bio' => 'nullable|string|max:500',
            'preferred_position' => 'required|string',
            'skill_level' => 'required|in:beginner,intermediate,advanced,pro',
            'age' => 'required|integer|min:13|max:100',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'dominant_foot' => 'required|in:right,left,both',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
        ];
    }
}
