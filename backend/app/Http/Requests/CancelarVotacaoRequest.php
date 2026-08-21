<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelarVotacaoRequest extends FormRequest
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
            'motivo_cancelamento' => [
                'required',
                'string',
                'max:1000'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_cancelamento.required' =>
            'O motivo do cancelamento é obrigatório.',

            'motivo_cancelamento.string' =>
            'O motivo do cancelamento deve ser um texto.',

            'motivo_cancelamento.max' =>
            'O motivo do cancelamento não pode possuir mais de 1.000 caracteres.',
        ];
    }
}
