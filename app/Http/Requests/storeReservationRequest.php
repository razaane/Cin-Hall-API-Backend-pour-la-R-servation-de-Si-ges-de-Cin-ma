<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class storeReservationRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'seance_id'=> "required|exists:seances,id",
        ];
    }
     public function messages()
    {
        return [
            'seance_id.required' => 'Vous devez sélectionner une séance.',
            'seances.exists' => 'Cette séance n’existe pas.',
        ];
    }
}
