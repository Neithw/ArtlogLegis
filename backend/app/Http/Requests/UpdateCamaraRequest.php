<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCamaraRequest extends FormRequest
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
            'nome' => [
                'required',
                'string',
                'max:255',
            ],

            'cnpj' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^\d{14}$/',

                Rule::unique('camaras', 'cnpj')
                    ->ignore($this->route('camara')),
            ],

            'ativo' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode possuir mais de 255 caracteres.',

            'cnpj.size' => 'O CNPJ deve conter exatamente 14 dígitos.',
            'cnpj.regex' => 'O CNPJ deve conter apenas números.',
            'cnpj.unique' => 'O CNPJ já existe.',

            'ativo.required' => 'Informe se a Câmara está ativa.',
            'ativo.boolean' => 'O status informado é inválido.',
        ];
    }
}
