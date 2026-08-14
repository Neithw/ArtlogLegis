<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoProposicaoRequest extends FormRequest
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
        $tipoProposicao = $this->route('tipoProposicao');

        $this->merge([
            'camara_id' => $tipoProposicao->camara_id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tipoProposicao = $this->route('tipoProposicao');

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
                Rule::unique('tipos_proposicao', 'nome')
                    ->where(
                        fn($query) => $query->where(
                            'camara_id',
                            $this->integer('camara_id')
                        )
                    )
                    ->ignore($tipoProposicao),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'camara_id.required' => 'Selecione uma Câmara.',
            'camara_id.integer' => 'A Câmara selecionada é inválida.',
            'camara_id.exists' => 'A Câmara selecionada não está disponível.',

            'nome.required' => 'Informe o nome do tipo de proposição.',
            'nome.string' => 'O nome do tipo de proposição deve ser um texto válido.',
            'nome.max' => 'O nome do tipo de proposição não pode ter mais de :max caracteres.',
            'nome.unique' => 'Já existe um tipo de proposição com esse nome nesta Câmara.',
        ];
    }
}
