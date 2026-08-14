<?php

namespace App\Http\Requests;

use App\Models\UnidadeTramitacao;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnidadeTramitacaoRequest extends FormRequest
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
        $sigla = $this->string('sigla')
            ->trim()
            ->upper()
            ->value();

        $dados = [
            'sigla' => $sigla !== '' ? $sigla : null
        ];

        if (! $this->user()->isRoot()) {
            $dados['camara_id'] = $this->user()->camara_id;
        }

        $this->merge($dados);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'camara_id' => [
                'required',
                'integer',
                Rule::exists('camaras', 'id')
                    ->where('ativo', true)
            ],

            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('unidades_tramitacao', 'nome')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $this->input('camara_id'))
                    )
            ],

            'sigla' => [
                'nullable',
                'string',
                'max:50'
            ],

            'tipo' => [
                'required',
                'string',
                Rule::in(array_keys(UnidadeTramitacao::TIPOS))
            ],

            'descricao' => [
                'nullable',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'camara_id.required' => 'Selecione uma Câmara.',
            'camara_id.exists' => 'A Câmara selecionada é inválida ou está indisponível.',
            'nome.required' => 'Informe o nome da unidade.',
            'nome.max' => 'O nome da unidade deve ter no máximo 255 caracteres.',
            'nome.unique' => 'Já existe uma unidade com este nome nesta Câmara.',
            'sigla.max' => 'A sigla deve ter no máximo 50 caracteres.',
            'tipo.required' => 'Selecione o tipo da unidade.',
            'tipo.in' => 'Selecione um tipo de unidade válido.',
            'descricao.string' => 'A descrição deve ser um texto válido.',
        ];
    }
}
