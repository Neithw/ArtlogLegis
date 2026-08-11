<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProposicaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $usuarioAutenticado = $this->user();

        $dados = [
            'palavras_chave' => $this->input('palavras_chave', []),
        ];

        if (! $usuarioAutenticado->isRoot()) {
            $dados['camara_id'] = $usuarioAutenticado->camara_id;
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
                    ->whereNull('deleted_at')
                    ->where('ativo', true)
            ],

            'legislatura_id' => [
                'required',
                'integer',
                Rule::exists('legislaturas', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $this->input('camara_id'))
                            ->whereNull('deleted_at')
                    )
            ],

            'tipo_proposicao_id' => [
                'required',
                'integer',
                Rule::exists('tipos_proposicao', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $this->input('camara_id'))
                            ->whereNull('deleted_at')
                    )
            ],

            'autor_mandato_id' => [
                'required',
                'integer',
                Rule::exists('mandatos', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('legislatura_id', $this->input('legislatura_id'))
                            ->whereNull('deleted_at')
                            ->whereIn(
                                'vereador_id',
                                function ($query) {
                                    $query
                                        ->select('id')
                                        ->from('vereadores')
                                        ->where('camara_id', $this->input('camara_id'));
                                }
                            )
                    ),
            ],

            'ementa' => [
                'nullable',
                'string',
            ],

            'texto_integral' => [
                'nullable',
                'string',
            ],

            'assunto' => [
                'nullable',
                'string',
                'max:255'
            ],

            'area_tematica' => [
                'nullable',
                'string',
                'max:255'
            ],

            'palavras_chave' => [
                'nullable',
                'array',
            ],

            'palavras_chave.*' => [
                'distinct:ignore_case',
                'string',
                'max:100'
            ],
        ];
    }
}
