<?php

namespace App\Http\Requests;

use App\Models\UnidadeTramitacao;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnidadeTramitacaoRequest extends FormRequest
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
        $unidadeTramitacao = $this->route('unidadeTramitacao');

        $this->merge([
            'camara_id' => $unidadeTramitacao->camara_id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unidadeTramitacao = $this->route('unidadeTramitacao');

        return [
            'camara_id' => [
                'required',
                'integer',
            ],

            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('unidades_tramitacao', 'nome')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $unidadeTramitacao->camara_id)
                    )
                    ->ignore($unidadeTramitacao)
            ],

            'sigla' => [
                'nullable',
                'string',
                'max:50'
            ],

            'tipo' => [
                'required',
                'string',
                Rule::in(UnidadeTramitacao::TIPOS)
            ],

            'descricao' => [
                'nullable',
                'string'
            ]
        ];
    }
}
