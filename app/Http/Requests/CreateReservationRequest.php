<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'party_size' => ['required', 'integer', 'min:1'],
            'restaurant_id' => ['nullable', 'integer', 'exists:restaurants,id'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}
