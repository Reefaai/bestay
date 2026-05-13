<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminPaymentIndexRequest extends FormRequest
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
            'status'     => ['nullable', Rule::in(Payment::STATUSES)],
            'method'     => ['nullable', Rule::in(Payment::METHODS)],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'page'       => ['nullable', 'integer', 'min:1'],
        ];
    }
}
