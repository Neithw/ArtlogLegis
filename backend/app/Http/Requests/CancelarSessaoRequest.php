<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelarSessaoRequest extends FormRequest
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
            'observacao' => [
                'required',
                'string',
                'min:3',
                'max:2000'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'observacao.required' => 'A justificativa do cancelamento é obrigatória.',
            'observacao.string' => 'A justificativa do cancelamento é inválida.',
            'observacao.min' => 'A justificativa deve possuir pelo menos 3 caracteres.',
            'observacao.max' => 'A justificativa não pode possuir mais de 2000 caracteres.'
        ];
    }
}
