<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCamaraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cnpj' => $this->filled('cnpj')
                ? preg_replace('/\D/', '', (string) $this->input('cnpj'))
                : null
        ]);
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
                'unique:camaras,cnpj',
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
        ];
    }
}
