<?php

namespace App\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;

class NearbyStationsRequest extends FormRequest
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
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lat.required' => 'A latitude é obrigatória.',
            'lat.numeric' => 'A latitude deve ser um número.',
            'lat.between' => 'A latitude deve estar entre -90 e 90.',
            'lng.required' => 'A longitude é obrigatória.',
            'lng.numeric' => 'A longitude deve ser um número.',
            'lng.between' => 'A longitude deve estar entre -180 e 180.',
            'radius.numeric' => 'O raio deve ser um número.',
            'radius.min' => 'O raio deve ser pelo menos 0.1 km.',
            'radius.max' => 'O raio deve ser no máximo 50 km.',
        ];
    }
}
