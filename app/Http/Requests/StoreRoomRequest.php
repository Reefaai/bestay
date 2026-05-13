<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:standard,deluxe,suite'],
            'description' => ['nullable', 'string'],
            'price_per_night' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'image_url' => ['nullable', 'url'],
            'is_active' => ['boolean'],
        ];
    }
}
