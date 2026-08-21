<?php

namespace App\Http\Requests;

use App\Models\Votacao;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AbrirVotacaoRequest extends FormRequest
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
            'tipo' => $this->filled('tipo')
                ? $this->input('tipo')
                : 'nominal',

            'criterio_aprovacao' => $this->filled('criterio_aprovacao')
                ? $this->input('criterio_aprovacao')
                : 'maioria_simples'
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
            'tipo' => [
                'required',
                Rule::in(array_keys(Votacao::TIPOS))
            ],

            'criterio_aprovacao' => [
                'required',
                Rule::in(array_keys(Votacao::CRITERIOS_APROVACAO))
            ],

            'observacao' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'O tipo da votação é obrigatório.',
            'tipo.in' => 'O tipo de votação selecionado é inválido.',

            'criterio_aprovacao.required' => 'O critério de aprovação é obrigatório.',
            'criterio_aprovacao.in' => 'O critério de aprovação selecionado é inválido.',

            'observacao.string' => 'A observação deve ser um texto.',
            'observacao.max' => 'A observação não pode possuir mais de 1.000 caracteres.',
        ];
    }
}
