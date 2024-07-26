<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EventRequest extends FormRequest
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
            //
            'name' => 'required|string|max:255', //Le nom (obligatoire)
            'description' => 'nullable|string', //La description (optionnelle)
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048', //L'image (optionnelle)
            'price' => 'required|numeric|min:0',
            'country' => 'required|string',
            'address' => 'nullable|string',
            'startDate' => 'required|date_format:Y-m-d H:i:s|after_or_equal:now',
            'endDate' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:startDate', // Date de fin de la relation (optionnelle)
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
