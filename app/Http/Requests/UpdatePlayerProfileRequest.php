<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bio' => 'nullable|string|max:500',
            'preferred_position' => 'nullable|string',
            'skill_level' => 'nullable|in:beginner,intermediate,advanced,pro',
            'age' => 'nullable|integer|min:13|max:100',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'dominant_foot' => 'nullable|in:right,left,both',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
        ];
    }
}
