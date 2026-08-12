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
        if (! $this->user()->isRoot()) {
            $this->merge([
                'camara_id' => $this->user()->camara_id,
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
        return [
            'camara_id' => [
                'required',
                'integer',
                Rule::exists('camaras', 'id')
                    ->whereNull('deleted_at')
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
                Rule::in(UnidadeTramitacao::TIPOS)
            ],

            'descricao' => [
                'nullable',
                'string'
            ]
        ];
    }
}
