<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'size' => 'nullable|array',
            'size.*' => 'string|max:50', // Chaque taille doit être une chaîne de caractères d'une longueur maximale de 50
            'color' => 'nullable|array',
            'color.*' => 'string|max:50', // Chaque couleur doit être une chaîne de caractères d'une longueur maximale de 50
            'quantity' => 'required|integer|min:0',
            'imageUrl.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'stock' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
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
