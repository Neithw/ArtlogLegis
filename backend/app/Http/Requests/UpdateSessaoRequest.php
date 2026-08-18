<?php

namespace App\Http\Requests;

use App\Models\Legislatura;
use App\Models\Sessao;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateSessaoRequest extends FormRequest
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
        $sessao = $this->route('sessao');

        return [
            'legislatura_id' => [
                'required',
                'integer',
                Rule::exists('legislaturas', 'id')
                    ->where('camara_id', $sessao->camara_id)
                    ->whereNull('deleted_at')
            ],

            'data_hora_inicio_previsto' => [
                'required',
                Rule::date()->format('Y-m-d\TH:i')
            ],

            'tipo' => [
                'required',
                'string',
                Rule::in(array_keys(Sessao::TIPOS))
            ],

            'local' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }

    public function after(): array
    {

        return [
            function (Validator $validator): void {
                $sessao = $this->route('sessao');

                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $legislatura = Legislatura::query()
                    ->whereKey($this->integer('legislatura_id'))
                    ->where('camara_id', $sessao->camara_id)
                    ->first();

                if (! $legislatura) {
                    return;
                }

                $dataInicioLegislatura = $legislatura->data_inicio
                    ->copy()
                    ->startOfDay();

                $dataFimLegislatura = $legislatura->data_fim
                    ->copy()
                    ->endOfDay();

                $dataSessao = $this->date('data_hora_inicio_previsto');

                $dataForaDoPeriodo =
                    $dataSessao->lt($dataInicioLegislatura)
                    || $dataSessao->gt($dataFimLegislatura);

                if ($dataForaDoPeriodo) {
                    $validator->errors()->add(
                        'data_hora_inicio_previsto',
                        'A data da sessão deve estar dentro do período da legislatura.'
                    );
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'legislatura_id.required' => 'A legislatura é obrigatória.',
            'legislatura_id.integer' => 'A legislatura informada é inválida.',
            'legislatura_id.exists' => 'A legislatura informada não foi encontrada.',

            'data_hora_inicio_previsto.required' => 'A data e o horário de início são obrigatórios.',
            'data_hora_inicio_previsto.date_format' => 'A data e o horário de início informados são inválidos.',

            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.string' => 'O tipo informado é inválido.',
            'tipo.in' => 'O tipo informado é inválido.',

            'local.string' => 'O local informado é inválido.',
            'local.max' => 'O local não pode possuir mais de 255 caracteres.',
        ];
    }
}
