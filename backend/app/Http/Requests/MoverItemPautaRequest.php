<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoverItemPautaRequest extends FormRequest
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
            'direcao' => [
                'required',
                Rule::in(['acima', 'abaixo'])
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'direcao.required' => 'Informe a direção do item.',
            'direcao.in' => 'A direção informada é inválida.',
        ];
    }
}
