<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'in:standard,deluxe,suite'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price_per_night' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1', 'max:100'],
            'image_url' => ['sometimes', 'nullable', 'url'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
