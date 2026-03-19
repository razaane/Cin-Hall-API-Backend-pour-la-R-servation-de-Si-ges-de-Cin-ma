<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:normale,vip',
            'total_seats' => 'required|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'type.in' => 'Le type doit être normale ou vip',
            'total_seats.min' => 'Le nombre de sièges doit être supérieur à 0'
        ];
    }
    }
