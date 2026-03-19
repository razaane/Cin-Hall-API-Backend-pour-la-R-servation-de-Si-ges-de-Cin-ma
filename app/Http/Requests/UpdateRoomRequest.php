<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:normale,vip',
            'total_seats' => 'sometimes|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'type.in' => 'Le type doit être normale ou vip',
            'total_seats.integer' => 'Le nombre de sièges doit être un entier',
            'total_seats.min' => 'Le nombre de sièges doit être supérieur à 0'
        ];
    }
}
