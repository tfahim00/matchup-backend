<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location_id' => ['required', 'exists:locations,id'],
            'skill_level' => ['required', 'in:beginner,intermediate,advanced,mixed'],
            'match_type' => ['required', 'in:5v5,7v7,11v11,custom'],
            'slots_available' => ['required', 'integer', 'min:1'],
            'match_date' => ['required', 'date'],
            'status' => ['nullable', 'in:open,full,ongoing,completed,cancelled'],
            'visibility' => ['nullable', 'in:public,private'],
        ];
    }
}
