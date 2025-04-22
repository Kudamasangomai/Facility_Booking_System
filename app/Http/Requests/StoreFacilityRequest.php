<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
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

            'name' => 'required|min:3|unique:facilities,name',
            'description' => 'required|min:10',
            'capacity' => 'required',
            'specialnote' => 'required|min:10',
            'price' => 'required',
            // 'images.*'  => 'required|mimes:jpeg,png,jpg,gif|max:2048',


        ];
    }
}
