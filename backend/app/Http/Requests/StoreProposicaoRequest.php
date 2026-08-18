<?php

namespace App\Http\Requests;

use App\Models\Mandato;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProposicaoRequest extends FormRequest
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
        $usuarioAutenticado = $this->user();

        $palavrasChave = $this->input('palavras_chave', []);

        $dados = [
            'palavras_chave' => is_array($palavrasChave)
                ? collect($palavrasChave)
                ->filter(fn($valor) => is_string($valor))
                ->map(fn($valor) => trim($valor))
                ->filter(fn($valor) => $valor !== '')
                ->values()
                ->all()
                : $palavrasChave,
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

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (
                    $validator->errors()->has('camara_id')
                    || $validator->errors()->has('legislatura_id')
                    || $validator->errors()->has('autor_mandato_id')
                ) {
                    return;
                }

                $mandatoValido = Mandato::query()
                    ->whereKey($this->integer('autor_mandato_id'))
                    ->where(
                        'legislatura_id',
                        $this->integer('legislatura_id')
                    )
                    ->vigenteEm(today())
                    ->whereHas(
                        'vereador',
                        fn($query) => $query->where(
                            'camara_id',
                            $this->integer('camara_id')
                        )
                    )
                    ->exists();

                if (! $mandatoValido) {
                    $validator->errors()->add(
                        'autor_mandato_id',
                        'O autor principal deve possuir mandato vigente na legislatura selecionada.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'camara_id.required' => 'Selecione uma Câmara.',
            'camara_id.exists' => 'A Câmara selecionada não está disponível.',

            'legislatura_id.required' => 'Selecione uma legislatura.',
            'legislatura_id.exists' => 'A legislatura selecionada não está disponível.',

            'tipo_proposicao_id.required' => 'Selecione um tipo de proposição.',
            'tipo_proposicao_id.exists' => 'O tipo de proposição selecionado não está disponível.',

            'autor_mandato_id.required' => 'Selecione o autor principal.',
            'autor_mandato_id.exists' => 'O mandato selecionado não está disponível.',

            'ementa.string' => 'A ementa deve ser um texto válido.',
            'texto_integral.string' => 'O texto integral deve ser um texto válido.',

            'assunto.string' => 'O assunto deve ser um texto válido.',
            'assunto.max' => 'O assunto não pode ter mais de :max caracteres.',

            'area_tematica.string' => 'A área temática deve ser um texto válido.',
            'area_tematica.max' => 'A área temática não pode ter mais de :max caracteres.',

            'palavras_chave.array' => 'As palavras-chave possuem um formato inválido.',
            'palavras_chave.*.distinct' => 'As palavras-chave não podem ser repetidas.',
            'palavras_chave.*.string' => 'Cada palavra-chave deve ser um texto válido.',
            'palavras_chave.*.max' => 'Cada palavra-chave pode ter no máximo :max caracteres.',
        ];
    }
}
