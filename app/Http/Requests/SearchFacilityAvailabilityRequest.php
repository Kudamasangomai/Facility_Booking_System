<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchFacilityAvailabilityRequest extends FormRequest
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

            // 'facility_name' => 'required|string|exists:facilities,name',
            'check_in' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'check_out' => 'required|date|date_format:Y-m-d|after_or_equal:check_in',
       
        ];
    }
}
