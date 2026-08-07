<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartidoRequest extends FormRequest
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
        if ($this->sigla) {
            $this->merge([
                'sigla' => strtoupper($this->sigla)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $partido = $this->route('partido');

        return [
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('partidos', 'nome')
                    ->ignore($partido)
            ],

            'sigla' => [
                'required',
                'string',
                'max:255',
                Rule::unique('partidos', 'sigla')
                    ->ignore($partido)
            ],

            'numero_eleitoral' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('partidos', 'numero_eleitoral')
                    ->ignore($partido)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto.',
            'nome.max' => 'O nome é muito extenso.',
            'nome.unique' => 'Este nome já está cadastrado.',

            'sigla.required' => 'A sigla é obrigatória.',
            'sigla.string' => 'A sigla deve ser um texto.',
            'sigla.max' => 'A sigla é muito extensa.',
            'sigla.unique' => 'Esta sigla já está cadastrada.',

            'numero_eleitoral.integer' => 'O número eleitoral deve ser um número inteiro.',
            'numero_eleitoral.min' => 'O número eleitoral deve ser maior que 0.',
            'numero_eleitoral.max' => 'O número eleitoral é inválido.',
            'numero_eleitoral.unique' => 'Este número eleitoral já está cadastrado.',
        ];
    }
}
