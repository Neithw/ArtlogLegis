<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateMandatoRequest extends FormRequest
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
        return [
            'data_inicio' => [
                'required',
                'date'
            ],

            'data_fim' => [
                'nullable',
                'date',
                'after_or_equal:data_inicio'
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['data_inicio', 'data_fim'])) {
                    return;
                }

                $mandato = $this->route('mandato');
                $legislatura = $mandato->legislatura;

                $dataInicio = Carbon::parse($this->input('data_inicio'));

                $dataFim = $this->filled('data_fim')
                    ? Carbon::parse($this->input('data_fim'))
                    : null;

                if ($dataInicio->lt($legislatura->data_inicio)) {
                    $validator->errors()->add(
                        'data_inicio',
                        'A data de início do mandato não pode ser anterior ao início da legislatura.'
                    );
                }

                if ($dataInicio->gt($legislatura->data_fim)) {
                    $validator->errors()->add(
                        'data_inicio',
                        'A data de início do mandato não pode ser posterior ao fim da legislatura.'
                    );
                }

                if ($dataFim && $dataFim->gt($legislatura->data_fim)) {
                    $validator->errors()->add(
                        'data_fim',
                        'A data de término do mandato não pode ser posterior ao fim da legislatura.'
                    );
                }

                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $filiacoes = $mandato->filiacoesPartidarias()
                    ->orderBy('data_inicio')
                    ->get();

                if ($filiacoes->isEmpty()) {
                    return;
                }

                $primeiraFiliacao = $filiacoes->first();
                $ultimaFiliacao = $filiacoes->last();

                foreach ($filiacoes as $filiacao) {
                    $inicioEfetivo = $filiacao->is($primeiraFiliacao)
                        ? $dataInicio
                        : $filiacao->data_inicio;

                    $fimEfetivo = $filiacao->is($ultimaFiliacao)
                        ? $dataFim
                        : $filiacao->data_fim;

                    if ($inicioEfetivo->lt($dataInicio)) {
                        $validator->errors()->add(
                            'data_inicio',
                            'A data de início do mandato não pode ultrapassar uma filiação partidária já registrada.'
                        );

                        return;
                    }

                    if ($dataFim && $inicioEfetivo->gt($dataFim)) {
                        $validator->errors()->add(
                            'data_fim',
                            'A data de término do mandato não pode ser anterior a uma filiação partidária já registrada.'
                        );

                        return;
                    }

                    if ($fimEfetivo && $fimEfetivo->lt($inicioEfetivo)) {
                        $validator->errors()->add(
                            'data_fim',
                            'O período informado é incompatível com o histórico partidário do mandato.'
                        );

                        return;
                    }

                    if ($dataFim && $fimEfetivo && $fimEfetivo->gt($dataFim)) {
                        $validator->errors()->add(
                            'data_fim',
                            'A data de término do mandato não pode ser anterior ao término de uma filiação partidária já registrada.'
                        );

                        return;
                    }
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'data_inicio.required' => 'Informe a data de início do mandato.',
            'data_inicio.date' => 'Informe uma data de início válida.',

            'data_fim.date' => 'Informe uma data de término válida.',
            'data_fim.after_or_equal' =>
            'A data de término deve ser igual ou posterior à data de início.',
        ];
    }
}
