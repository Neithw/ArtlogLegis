<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProposicaoRequest extends FormRequest
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
        $proposicao = $this->route('proposicao');

        $this->merge([
            'camara_id' => $proposicao->camara_id,
            'palavras_chave' => $this->input('palavras_chave', [])
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $proposicao = $this->route('proposicao');

        return [
            'legislatura_id' => [
                'required',
                'integer',
                Rule::exists('legislaturas', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $this->input('camara_id'))
                            ->where(
                                function ($query) use ($proposicao) {
                                    $query
                                        ->whereNull('deleted_at')
                                        ->orWhere('id', $proposicao->legislatura_id);
                                }
                            )
                    )
            ],

            'tipo_proposicao_id' => [
                'required',
                'integer',
                Rule::exists('tipos_proposicao', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $this->input('camara_id'))
                            ->where(
                                function ($query) use ($proposicao) {
                                    $query
                                        ->whereNull('deleted_at')
                                        ->orWhere('id', $proposicao->tipo_proposicao_id);
                                }
                            )
                    )
            ],

            'autor_mandato_id' => [
                'required',
                'integer',
                Rule::exists('mandatos', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('legislatura_id', $this->input('legislatura_id'))
                            ->where(
                                function ($query) use ($proposicao) {
                                    $query
                                        ->whereNull('deleted_at')
                                        ->orWhere('id', $proposicao->autor_mandato_id);
                                }
                            )
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
