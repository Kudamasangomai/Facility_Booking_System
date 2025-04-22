<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
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
            'id' => 'exists:facilities,id',
            'name' => 'required|min:3|'. Rule::unique('facilities')->ignore($this->facility),Rule::exists('facilities'),
            'description' => 'required|min:10',
            'capacity' => 'required',
            'specialnote' => 'required|min:10',
            'price' => 'required',
   
        ];
    }
}
