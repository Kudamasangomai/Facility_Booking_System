<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'check_in' => 'required|after_or_equal:today',
            'check_out' => 'required|after_or_equal:check_in',
            'attendants' => 'required|numeric',
            'purpose' => 'required|alpha',
            'facility_id' =>'required|exists:facilities,id'
        ];
    }
}
