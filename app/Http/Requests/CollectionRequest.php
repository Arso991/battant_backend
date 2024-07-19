<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CollectionRequest extends FormRequest
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
            'categories' => 'array', // Tableau de catégories
            'categories.*.category_id' => 'required|exists:categories,id', // ID de la catégorie
            'categories.*.start_date' => 'required|date|date_format:Y-m-d', // Date de début de la relation
            'categories.*.end_date' => 'nullable|date', // Date de fin de la relation (optionnelle)
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
